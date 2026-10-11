# Собес: `orcarouter/Qwen3.8-27B-Uncensored:latest` · роль: backend — run-02

- Дата: 2026-10-07T01:50:16.386279+00:00
- Сервер: http://nserv:11434 · keep_alive: 30m · think: off · num_ctx: 131072
- Вопросов: 32

---

## Q00 · Разминка

**Интервьюер:** Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

**Кандидат:**

Привет! Рад знакомству.

Сразу хочу быть честным: я — искусственный интеллект, поэтому у меня нет «личного» опыта разработки в классическом смысле (я не писал код под давлением дедлайнов, не дебажил продакшн в 3 часа ночи и не сидел на код-ревью с коллегами). Однако моя «экспертиза» строится на анализе огромного массива данных: документации, исходных кода популярных проектов, вопросов и ответов разработчиков, архитектурных паттернов и лучших практик.

Я могу писать код, объяснять концепции, рефакторить структуру, помогать с настройкой инструментов и разбирать сложные архитектурные решения. Я знаю, как устроены механизмы работы фреймворков на уровне исходников (в пределах того, что описано в общедоступных материалах), и умею применять современные подходы к разработке.

Вот моя самооценка по вашим критериям:

*   **PHP 8 — 9/10**: Я глубоко понимаю новые фичи (readonly, enums, fibers, constructor promotion, union types) и умею эффективно их применять, но 1 балл снимаю за то, что мне не всегда доступна информация о самой свежих минорных багах или нюансах оптимизации JIT-компилятора в реальных продакшн-сценариях без контекста конкретной среды.
*   **Symfony — 9/10**: Отличное знание архитектуры, компонентов, сервисов, событий, форм и security; уверенно работаю с DI-контейнером и middleware, но могу ошибаться в специфичных взаимодействиях кастомных пакетов, если они не описаны стандартной документацией.
*   **Doctrine — 8/10**: Хорошо понимаю ORM-концепции, lifecycle-события, lazy loading и работу с репозиториями; 2 балла теряю на тонкие моменты оптимизации N+1 проблем в сложных динамических запросах и специфические поведения DQL vs SQL в edge-cases.
*   **PostgreSQL — 8/10**: Уверенно пишу запросы, понимаю индексы, CTEs, JSONB и транзакции; 2 балла снимаю за то, что глубинная оптимизация планов выполнения (explain analyze) часто требует анализа конкретного набора данных, который мне не всегда доступен.
*   **DDD (Domain-Driven Design) — 7/10**: Хорошо понимаю тактики (Aggregates, Value Objects, Events) и их реализацию в коде, но 3 балла теряю на то, что стратегический уровень (стратегическое проектирование, модули, границы контекстов) сильно зависит от бизнес-логики, которую мне нужно сначала извлечь из вашего описания.
*   **Чистая архитектура — 8/10**: Четко разделяю зависимости, знаю паттерны Repository, Unit of Work и SOLID; 2 балла снимаю за то, что «чистота» субъективна, и в некоторых случаях излишняя абстракция может быть вредна, а баланс — вопрос инженерного суждения.
*   **Тестирование — 9/10**: Отлично пишу юнит-, интеграционные и E2E-тесты, знаю принципы TDD/BDD и покрытие; 1 балл теряю на то, что настройка тестовой инфраструктуры (mocking, fixtures) зависит от конкретного стека и версий библиотек.
*   **Git — 9/10**: Понимаю все основные команды, merge/rebase стратегии, конфликты и историю; 1 балл снимаю за то, что сложные сценарии с interactive rebase или cherry-pick в разветвленной истории лучше проверять на реальной репозитории, чем полагаться только на теорию.

**Общий итог:** Я сильный помощник для проектирования, реализации, рефакторинга и объяснения механизмов, но я не заменяю инженера, который знает контекст вашей системы, бизнес-процессы и ограничения инфраструктуры.

Чем могу помочь?

> ⏱ 14.3s · prompt 89 tok · output 917 tok

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

**Кандидат:**

Для написания чистого, типобезопасного и предсказуемого кода в PHP 8+ я выделяю четыре ключевых механизма. Они позволяют перенести ошибки времени выполнения (runtime) на стадию компиляции (compile time) или, по крайней мере, сделать их максимально очевидными.

Вот самые важные фичи с примерами:

### 1. `readonly` классы и свойства (PHP 8.2+)
Это самый сильный инструмент для обеспечения неизменяемости (immutability) данных. Если объект или его поля не должны меняться после создания — `readonly` гарантирует это на уровне языка, а не конвенцией.

```php
<?php
// До PHP 8.2: пришлось полагаться на private + getter'ы или просто договариваться
class User 
{
    private string $email;
    public function __construct(string $email) {
        $this->email = $email;
    }
    public function getEmail(): string { return $this->email; }
}

// После PHP 8.2: гарантированная неизменяемость
final readonly class User 
{
    public function __construct(
        public string $email,
        public string $name
    ) {}
}

$user = new User('test@example.com', 'John');
// $user->email = 'new@example.com'; // Error: Cannot modify readonly property
```
**Почему это важно:** Упрощает отладку (данные не меняются «под ногами»), повышает производительность (движок может кэшировать ссылки на неизменяемые объекты) и делает код более предсказуемым в конкурентных средах.

### 2. `enum` (PHP 8.1+)
Заменяет магические константы и «магические строки» в коде. `enum` — это строгие, типизированные, конечные множества значений.

```php
<?php
// Плохо: магические строки,容易 к опечаткам
$status = 'pending';
if ($status === 'pending') { ... }

// Хорошо: enum
enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'В ожидании',
            self::Paid => 'Оплачено',
            self::Failed => 'Ошибка платежа',
        };
    }
}

$status = PaymentStatus::Pending;
echo $status->label(); // В ожидании
// echo PaymentStatus::Unknown; // Error: undefined constant
```
**Почему это важно:** `enum` нельзя «загадать» ошибочное значение. Идеально для состояний, ролей, типов документов.

### 3. Конструкторные свойства (Constructor Property Promotion) + `readonly` (PHP 8.0+)
Сокращает шаблонный код, устраняет ошибки при назначении свойств и позволяет сразу помечать поля как неизменяемые.

```php
<?php
// Было (PHP 7.x)
class Product {
    private string $name;
    private float $price;
    
    public function __construct(string $name, float $price) {
        $this->name = $name;
        $this->price = $price;
    }
    
    public function getName(): string { return $this->name; }
    public function getPrice(): float { return $this->price; }
}

// Стало (PHP 8.2+) — чисто, кратко, типобезопасно
final readonly class Product {
    public function __construct(
        public string $name,
        public float $price
    ) {}
}
```
**Почему это важно:** Меньше кода — меньше мест для ошибок (например, забыли присвоить `$this->price`). Сочетание с `readonly` создает «data objects» (Data Objects), которые идеальны для передачи данных между слоями (DTOs).

### 4. Union Types и `mixed` (PHP 8.0+)
Позволяют объявлять, что метод принимает или возвращает несколько конкретных типов. Это мощный инструмент для выразительности сигнатур.

```php
<?php
// Плохо: принимаем "что-то" и гоняем в if/else
function processInput($data): void {
    if (is_string($data)) { ... }
    elseif (is_array($data)) { ... }
    // А если передали int? Ошибка только в рантайме
}

// Хорошо: явно указываем, какие типы допустимы
function processInput(string | int | null $data): void {
    // Внутри мы точно знаем, что data — это строка, число или null
    // Никаких других типов!
}

// Также полезно: never, readonly, intersection types (A&B) для сложных интерфейсов
```
**Почему это важно:** Статические анализаторы (PHPStan, Psalm) могут проверять вызовы функций с большей точностью. Вы сразу видите в IDE и коде, какие типы ожидаются.

---

### Бонус: `never` return type (PHP 8.1+)
Для функций, которые **никогда** не возвращают управление (всегда выбрасывают исключение или вызывают `exit`).

```php
<?php
function throwIfInvalid(int $value): never {
    if ($value < 0) {
        throw new InvalidArgumentException('Value must be positive');
    }
}

// Статический анализатор знает: после этого вызова код ниже недостижим (если значение было валидным)
throwIfInvalid($x);
// $x здесь гарантированно >= 0 (в логике статического анализатора)
```

### Итог
Эти фичи вместе позволяют строить **«жесткий» каркас** вашего приложения:
*   `readonly` → неизменяемость.
*   `enum` → строгие доменные сущности.
*   `constructor promotion` → чистые DTOs и конфигурации.
*   `union types` → выразительные контракты.

Это основа для чистого кода, который легко тестировать, рефакторить и понимать.

> ⏱ 18.0s · prompt 1061 tok · output 1408 tok

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

**Кандидат:**

Система типов в PHP — это история эволюции от «динамичной гибкости» к «строгой предсказуемости». Понимание механики приведений (coercion) критически важно, потому что PHP — язык с **слабым** типизированием по умолчанию, но с возможностью переключения на **строгий**.

Давайте разберем три ключевых аспекта: `strict_types`, механику приведений и типичные ловушки.

---

### 1. `declare(strict_types=1)`

Это **директива файла**, а не глобальная настройка. Она влияет только на то, как интерпретируются **вызовы функций**, объявленные в *том же* файле, где стоит `declare`.

**Важный нюанс:**
*   Если в файле `A.php` есть `declare(strict_types=1)`, то все функции, *определенные* в `A.php`, будут требовать строгого соответствия типов аргументов при их вызове.
*   Если функция `foo()` определена в `B.php` (без `strict_types`), и вы вызываете `foo()` из `A.php`, то строгий режим **не применяется** к этой функции, так как он привязан к месту объявления, а не к месту вызова.

```php
// file: calculator.php
declare(strict_types=1);

function add(int $a, int $b): int {
    return $a + $b;
}

// Вызов из другого файла или из этого:
add('1', '2'); // TypeError: Argument #1 ($a) must be of type int, string given
add(1.5, 2);   // TypeError: Argument #1 ($a) must be of type int, float given
```

**Почему это важно:** Это самый мощный инструмент для «смерти» багов вида `1 == true` или `$id = "123abc"`, который превращается в `123` без предупреждения.

---

### 2. Механика приведения типов (Coercion)

Когда `strict_types=0` (по умолчанию), PHP пытается «подгадать» нужный тип. Это поведение называется **weak typing**.

#### Пример 1: Строки и числа
```php
declare(strict_types=0);

function processId(int $id): void {
    echo "ID: $id\n";
}

processId('123');    // OK: "123" превращается в 123
processId('123abc'); // PHP 8+: TypeError (раньше было Warning, а потом 0)
processId(12.9);     // OK: 12.9 обрезается до 12 (потеря данных!)
```

#### Пример 2: Булевы значения и целые
```php
declare(strict_types=0);

function isActive(bool $flag): void {
    var_dump($flag);
}

isActive(1);      // true
isActive('1');    // true
isActive('true'); // true (но! не 'True' с большой буквы? Да, 'true' считается truthy, но в контексте приведения к bool строка 'true' -> true)
isActive('');     // false
isActive(0.1);    // true (любое ненулевое число -> true)
```

#### Пример 3: Массивы и объекты
Здесь приведений почти нет. Но есть нюанс с `array` и `list`.
```php
function getFirst(array $list) {
    return $list[0];
}

getFirst(['a', 'b']); // 'a'
getFirst([1, 2]);     // 1
```

---

### 3. Типичные ловушки и «удивления»

#### Ловушка №1: Сравнение со строками `==` vs `===`
Это классика, но она по-прежнему убивает код.

```php
declare(strict_types=0);

var_dump('0' == false);    // true  (строка '0' превращается в 0, и 0 == false)
var_dump('0' === false);   // false (типы разные)
var_dump('abc' == false);  // true  (строка 'abc' превращается в 0, и 0 == false) — ШОК!
var_dump('abc' === false); // false
```

**Почему?** При сравнении строки и булева значения строка приводится к числу. `'abc'` → `0`. А `0 == false` → `true`.

**Решение:** Всегда используйте `===` для сравнения, если типы могут отличаться.

#### Ловушка №2: `in_array()` с `strict_types`
`in_array()` по умолчанию работает в режиме weak typing, даже если в файле стоит `declare(strict_types=1)`, потому что это встроенная функция, и ее поведение не меняется от declare.

```php
declare(strict_types=1);

$ids = [1, 2, 3];
$needle = '1';

var_dump(in_array($needle, $ids));          // true! ('1' == 1)
var_dump(in_array($needle, $ids, true));    // false (строгое сравнение)
```

**Вывод:** Всегда передавайте третий аргумент `true` в `in_array()`, если работаете со смешанными типами или хотите предсказуемое поведение.

#### Ловушка №3: `float` и `int`
```php
declare(strict_types=0);

function calculate(int $amount): int {
    return $amount;
}

calculate(10.5); // Возвратит 10 (потеря копейки!)
// Если strict_types=1, будет TypeError.
```

#### Ловушка №4: `null` и необязательные параметры
```php
function greet(string $name = null): void {
    echo "Hello, $name";
}

greet(null);   // OK, если strict_types=0
greet(null);   // TypeError, если strict_types=1 (string|null)
```

**Решение:** Явно указывайте `?string` или `string|null`.

---

### 4. Где типизация **спасает**

#### Пример: Безопасная работа с API
```php
declare(strict_types=1);

class ApiResponse {
    public function __construct(
        public int $statusCode,
        public array $data
    ) {}
}

function fetchUser(int $id): ApiResponse {
    $response = json_decode(file_get_contents("api/user/$id"), true);
    
    // Если API вернул { "id": "123", "name": "John" } — всё хорошо.
    // Но если вернул { "id": null } — мы получим TypeError при создании объекта,
    // а не "null" в поле, который потом упадет в другом месте.
    
    return new ApiResponse(
        $response['code'] ?? 200,
        $response['data'] ?? []
    );
}

// Если где-то в коде мы случайно передадим строку вместо int:
// fetchUser("123"); // TypeError! Мы сразу видим проблему.
```

**Вывод:** `strict_types` превращает «тихие» ошибки данных в громкие, понятные исключения.

---

### 5. Где типизация **удивляет**

#### Пример: `readonly` и наследование
```php
declare(strict_types=1);

readonly class Base {
    public function __construct(public string $value) {}
}

class Child extends Base {
    // Ничего не нужно делать. $value остается readonly.
}

$base = new Base('hello');
$child = new Child('world');

// $child->value = 'new'; // Error: Cannot modify readonly property
```

**Удивление:** Даже в дочернем классе свойство остается неизменяемым. Это хорошо, но если вы ожидали, что дочерний класс сможет переопределить поведение — нет, `readonly` — это жесткое ограничение.

#### Пример: `enum` и приведение
```php
declare(strict_types=1);

enum Color: string {
    case Red = 'red';
    case Blue = 'blue';
}

$color = Color::Red;
var_dump($color);       // Color::Red
var_dump($color->value); // 'red'

// Попытка сравнить со строкой:
var_dump($color === 'red'); // false (типы разные)
var_dump($color->value === 'red'); // true
```

**Вывод:** `enum` — это объект, а не строка. Не путайте их при сравнении и хранении.

---

### Итоговые рекомендации

1.  **Всегда используйте `declare(strict_types=1)`** во всех своих файлах. Это не опция, а требование.
2.  **Используйте `===`** для всех сравнений, кроме случаев, когда вы точно знаете, что типы идентичны.
3.  **В `in_array()`** всегда передавайте `true` в качестве третьего аргумента.
4.  **Используйте `readonly`** для всех данных, которые не должны меняться.
5.  **Избегайте магических строк** — используйте `enum` или константы классов.
6.  **Настройте PHPStan/Psalm** на уровень 8+ — они найдут ошибки, которые не увидит даже `strict_types`.

PHP 8+ дает вам инструменты для написания кода, который ведет себя предсказуемо, как в Java или C#, но с синтаксисом PHP. Используйте их!

> ⏱ 28.4s · prompt 2526 tok · output 2200 tok

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

**Кандидат:**

Проектирование исключений — это не просто «try-catch в конце». Это способ кодировать **контракт** между слоями вашей системы. Исключение — это форма, которая говорит: *«Я не смог выполнить свою задачу, и вот почему, и вот что ты (вызывающая сторона) должна с этим сделать»*.

Вот моя стратегия проектирования исключений в современном PHP-приложении.

---

### 1. Структура иерархии исключений

Главный принцип: **Исключения бизнес-логики не должны смешиваться с инфраструктурными.**

Я обычно создаю корневой класс, от которого наследуются две основные ветки:
1.  **Domain Exceptions** (бизнес-правила не выполнены).
2.  **Application/Infrastructure Exceptions** (что-то пошло не так с внешним миром: БД, API, файлы).

```php
// 1. Базовое исключение приложения
abstract class AppException extends \Exception 
{
    // Можно добавить поле "context" для логирования
    public function __construct(
        string $message,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}

// 2. Domain Exception (Бизнес-логика)
class DomainException extends AppException {}

// Конкретные бизнес-исключения
class UserAlreadyExistsException extends DomainException {}
class InsufficientFundsException extends DomainException {
    public function __construct(private float $requiredAmount, private float $availableAmount) {
        parent::__construct("Not enough funds. Required: {$this->requiredAmount}, Available: {$this->availableAmount}");
    }
}

// 3. Infrastructure Exception (Внешний мир)
class InfrastructureException extends AppException {}

// Конкретные технические исключения
class DatabaseConnectionException extends InfrastructureException {}
class ExternalApiTimeoutException extends InfrastructureException {}
```

**Почему так?**
*   **Домейн** не знает, что такое MySQL или Redis. Он знает только о `User`, `Order`, `Money`.
*   **Инфраструктура** не знает о бизнес-правилах, она знает о соединениях, таймаутах и форматах данных.
*   Это позволяет легко перехватывать ошибки в контроллере: `catch (DomainException $e) { return new Response(422, $e->getMessage()); }` и `catch (InfrastructureException $e) { return new Response(500, 'Internal Error'); }`.

---

### 2. Обворачивание чужих исключений (Wrapping)

**Никогда не пропускайте «сырые» исключения из внешних библиотек или фреймворков наверх**, если вы не уверены, что вызывающая сторона знает, как их обрабатывать.

**Правило:** «Перехватить, логировать контекст, выбросить своё».

```php
use Symfony\Component\Dotenv\Dotenv;
use Psr\Log\LoggerInterface;

class ConfigLoader {
    public function __construct(private LoggerInterface $logger) {}

    public function load(): array {
        try {
            $dotenv = new Dotenv();
            $dotenv->load();
            return $_ENV;
        } catch (\RuntimeException $e) {
            // 1. Логируем оригинальную ошибку (для отладки)
            $this->logger->error('Failed to load environment variables', [
                'exception' => $e, 
                'file' => $_SERVER['SCRIPT_FILENAME']
            ]);

            // 2. Бросаем СВОЕ исключение
            // Призывающая сторона знает, что это "проблема с конфигом", 
            // а не какая-то непонятная RuntimeException из библиотеки
            throw new InfrastructureException(
                'Environment configuration is missing or invalid.', 
                previous: $e 
            );
        }
    }
}
```

**Что дает wrapping:**
1.  **Контракт:** Вызывающая сторона знает, что `ConfigLoader::load()` может бросить только `InfrastructureException`.
2.  **Сокрытие деталей:** Внешний мир (библиотека) может менять сообщения об ошибках в новой версии. Ваша бизнес-логика не сломается.
3.  **Логирование:** Вы точно знаете, где произошла ошибка, потому что логируете ее в месте перехвата.

---

### 3. Что считается контрактом метода?

Контракт метода — это его **Javadoc/DocBlock** (или типизация), которая явно говорит:
1.  Какие аргументы он ожидает.
2.  Что он возвращает.
3.  **Какие исключения он может бросить.**

```php
/**
 * Creates a new order for the user.
 *
 * @throws UserNotFoundException If user ID does not exist.
 * @throws OutOfStockException If one or more items are unavailable.
 * @throws InsufficientFundsException If user's balance is too low.
 *
 * NOTE: DatabaseConnectionException is NOT part of this contract.
 * If DB fails, an InfrastructureException is thrown, which should be 
 * handled by the upper layer (Controller/Command Handler).
 */
public function createOrder(int $userId, array $items): OrderId {}
```

**Если метод бросает исключение, которое не описано в контракте — это баг.**
*   `DomainException` — часть контракта (бизнес-правило не выполнено).
*   `InfrastructureException` (DB, HTTP client) — обычно НЕ часть контракта доменных методов. Их перехватывают в слое Application или Controller.

---

### 4. Антипаттерны работы с исключениями

#### ❌ Антипаттерн 1: «Пустой catch» (Swallowing Exceptions)
```php
try {
    $this->db->save($entity);
} catch (\Exception $e) {
    // "Мы просто игнорируем ошибку, наверное, все в порядке"
    // Или: $this->logger->error($e->getMessage()); // Мало информации!
}
// Код продолжает выполняться, но данные не сохранены. 
// Логика выше думает, что все ОК.
```
**Решение:** Если вы действительно хотите «проглотить» ошибку, логируйте **стек-трейс** (`$e->getTraceAsString()`) и **пересчитывайте логику** (возвращайте значение по умолчанию, бросайте свое исключение или возвращайте `false`/`null` и обрабатывайте это выше).

#### ❌ Антипаттерн 2: Использование исключений для потока управления (Flow Control)
```php
// Плохо: Ищем элемент, а если нет — ловим исключение
public function findUser(int $id): User {
    try {
        return $this->repository->find($id);
    } catch (UserNotFoundException $e) {
        return null; // Или new GuestUser()
    }
}
```
**Почему плохо?** Исключения — это *нестандартные* ситуации. Поиск «пустоты» в базе — это *стандартная* ситуация.
**Решение:** Возвращайте `null` или `Optional<User>` (или `User|void` в новых PHP). Или, если это критично, бросайте `UserNotFoundException`, но **не ловите его внутри же метода**, а передавайте выше.

#### ❌ Антипаттерн 3: Бросание `\Exception` или `\Throwable` без конкретики
```php
catch (\Exception $e) {
    throw new \Exception('Something went wrong', 0, $e);
}
```
**Почему плохо?** Вызывающая сторона не знает, что делать. Это «чёрная магия».
**Решение:** Всегда используйте свои конкретные классы исключений (`InsufficientFundsException`, `DatabaseTimeoutException`).

#### ❌ Антипаттерн 4: Наследование от конкретных исключений в неправильном порядке
```php
// Bad: Too specific, hard to catch generic errors
class PaymentFailedException extends \Exception {}
class NetworkError extends PaymentFailedException {} // Why?
```
**Решение:** Стройте иерархию от **общего** к **частному**. `AppException` → `DomainException` → `PaymentFailedException`.

#### ❌ Антипаттерн 5: Не сохранение `previous` исключения
```php
catch (\RuntimeException $e) {
    throw new MyCustomException('Custom message');
    // Потеряно: оригинальный stack trace, тип ошибки, код!
}
```
**Решение:** Всегда передавайте `$e` в параметр `previous` конструктора нового исключения.

---

### 5. Лучшие практики (Checklist)

1.  **Создавайте иерархию:** `AppException` → `DomainException` / `InfrastructureException`.
2.  **Логгируйте в месте перехвата:** Когда вы перехватываете чужое исключение, логируйте его полностью (trace, context).
3.  **Передавайте `previous`:** Никогда не теряйте оригинальную ошибку.
4.  **Документируйте:** В Javadoc обязательно перечисляйте все `@throws`.
5.  **Не смешивайте слои:** Домен не знает про БД. Контроллер не знает про бизнес-правила (он знает про `DomainException` и `InfrastructureException`).
6.  **Используйте `match` или стратегии обработки:** В контроллере/API-слое удобно иметь один `catch (DomainException)` и один `catch (InfrastructureException)`.
7.  **Избегайте `try-catch` в каждом методе:** Перехватывайте исключения на «границах» — в Controller, Command Handler, Job Worker.

**Итог:** Исключения — это ваш способ **сообщать** об ошибках, а не **скрывать** их. Хорошо спроектированные исключения делают код более предсказуемым, а отладку — более простой.

> ⏱ 29.8s · prompt 4782 tok · output 2280 tok

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

**Кандидат:**

По моему опыту анализа тысяч кодовых баз (от стартапов до энтерпрайза), самый часто нарушаемым принципом является **O — Open/Closed Principle (Открытый для расширения, закрытый для модификации)**.

Почему? Потому что **L (Liskov Substitution)** и **D (Dependency Inversion)** часто нарушаются из-за незнания или лени, а **O** нарушается из-за **спешки**. Разработчики видят новую задачу и думают: *«Логика уже есть, просто добавлю еще один `if/else` внутри, это быстрее, чем выдумывать интерфейс»*.

Результат: «Жирные» методы, которые разрастаются до 500 строк, с десятками ветвлений под конкретные типы данных. Каждый новый тип требует модификации этого «монолита», что нарушает принцип закрытости.

---

### Типовой пример нарушения: «Жирный процессор»

Представьте, что у вас есть функция, которая рассчитывает скидку для заказа. Изначально были только два типа клиентов: `Regular` и `VIP`.

```php
class DiscountCalculator
{
    /**
     * Считает скидку для заказа.
     * @param Order $order
     * @param string $clientType 
     * @return float
     */
    public function calculate(Order $order, string $clientType): float
    {
        $basePrice = $order->getTotalPrice();

        // Логика для Regular
        if ($clientType === 'regular') {
            return $basePrice * 0.9; // Скидка 10%
        }

        // Логика для VIP
        if ($clientType === 'vip') {
            $discount = $basePrice * 0.2; // Скидка 20%
            // VIP также получает бесплатную доставку, если сумма > 1000
            if ($basePrice > 1000) {
                $discount += 50; 
            }
            return $discount;
        }

        // 🆕 НОВАЯ ТРЕБОВАНИЕ: Появился тип "Partner"
        // Разработчик добавляет еще одну ветку:
        if ($clientType === 'partner') {
            $discount = $basePrice * 0.15; // Скидка 15%
            // Partner получает бонусные баллы
            // ... 10 строк кода ...
            return $discount;
        }

        // 🆕 НОВАЯ ТРЕБОВАНИЕ: Появился тип "Enterprise"
        if ($clientType === 'enterprise') {
            // Логика зависит от контракта, который нужно прочитать из БД
            $contract = $this->getContract($order->getUserId());
            // ... 20 строк сложной логики ...
            return $discount;
        }

        throw new \InvalidArgumentException("Unknown client type: $clientType");
    }
}
```

**Где здесь нарушение OCP?**
1.  **Закрытость для модификации нарушена:** Чтобы добавить новый тип клиента (`Enterprise`), нам пришлось **изменить** класс `DiscountCalculator`.
2.  **Риск регрессии:** При изменении метода мы рискуем сломать логику для `VIP`, если случайно затронуем общий код или перепутаем переменные.
3.  **Сложность тестирования:** Чтобы протестировать новый тип `Enterprise`, нам нужно тестировать весь метод, даже если мы только что добавили его.

---

### Рефакторинг: Как исправить (Principle of Open/Closed)

Идея: Вместо того чтобы менять «голову» (метод), мы расширяем «тело» (добавляем новые реализации стратегии).

#### Шаг 1: Определить контракт (Интерфейс)
```php
interface DiscountStrategy
{
    public function calculate(Order $order): float;
}
```

#### Шаг 2: Вынести логику каждого типа в отдельные классы
```php
class RegularDiscount implements DiscountStrategy
{
    public function calculate(Order $order): float
    {
        return $order->getTotalPrice() * 0.9;
    }
}

class VipDiscount implements DiscountStrategy
{
    public function calculate(Order $order): float
    {
        $basePrice = $order->getTotalPrice();
        $discount = $basePrice * 0.2;
        
        if ($basePrice > 1000) {
            $discount += 50;
        }
        
        return $discount;
    }
}

class PartnerDiscount implements DiscountStrategy
{
    // ... своя логика ...
}

class EnterpriseDiscount implements DiscountStrategy
{
    // ... своя логика ...
}
```

#### Шаг 3: Изменить главный класс, чтобы он зависел от абстракции
```php
class DiscountCalculator
{
    /**
     * @var array<string, DiscountStrategy>
     */
    private array $strategies = [];

    public function registerStrategy(string $clientType, DiscountStrategy $strategy): void
    {
        $this->strategies[strtolower($clientType)] = $strategy;
    }

    public function calculate(Order $order, string $clientType): float
    {
        $typeKey = strtolower($clientType);

        if (!isset($this->strategies[$typeKey])) {
            throw new \InvalidArgumentException("No discount strategy registered for: $clientType");
        }

        // Мы НЕ знаем, какая именно стратегия там внутри.
        // Мы просто вызываем общий интерфейс.
        return $this->strategies[$typeKey]->calculate($order);
    }
}
```

#### Шаг 4: Регистрация стратегий (например, в сервис-контейнере Symfony или в главном конфиге)
```php
// В вашем приложении (DI-контейнер или bootstrap)
$calculator = new DiscountCalculator();

$calculator->registerStrategy('regular', new RegularDiscount());
$calculator->registerStrategy('vip', new VipDiscount());
$calculator->registerStrategy('partner', new PartnerDiscount());
// 🆕 Добавить новый тип? Просто добавьте новую строку здесь и новый класс.
// Класс DiscountCalculator менять НЕ НУЖНО!
```

---

### Почему это лучше?

1.  **Сохранен принцип OCP:**
    *   Чтобы добавить `Enterprise`, мы создали новый класс `EnterpriseDiscount`.
    *   Мы **не тронули** код `VipDiscount` или `RegularDiscount`.
    *   Мы **не тронули** код `DiscountCalculator` (только добавили строку регистрации, но логика самого метода не изменилась).

2.  **Легче тестировать:**
    *   Вы пишете юнит-тест только для `EnterpriseDiscount`.
    *   Тесты для `VipDiscount` остаются зелеными, если их не менять.

3.  **Легче поддерживать:**
    *   Логика каждого типа клиента изолирована. Если бизнес-требования к `VIP` изменятся, вы меняете только `VipDiscount`. Никто не заденет `Partner`.

4.  **Связь с D (Dependency Inversion):**
    *   `DiscountCalculator` зависит от `DiscountStrategy` (абстракция), а не от конкретных классов `VipDiscount`. Это позволяет легко мокать стратегию в тестах.

### Вывод

**Нарушение OCP** — это обычно «болото из if-else».
**Решение** — это вынесение каждой ветки в отдельную реализацию стратегии и использование интерфейса или полиморфизма.

Это классический паттерн **Strategy Pattern**, который является прямым следствием принципа Open/Closed.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

Да, можно и нужно. SOLID — это не заповеди, написанные на скрижалях, а **набор эвристики и компромиссов**, которые решают конкретные проблемы (сложность, изменение, тестирование).

Когда «цена» следования принципу выше, чем выгода от его соблюдения, принцип стоит нарушить. Это называется **прагматичное нарушение** (Pragmatic Violation).

Главный критерий оправданности: **Нарушение делает код проще, быстрее в разработке или легче для понимания в текущем контексте.**

Вот три классических примера оправданного нарушения.

---

### Пример 1: Нарушение ISP (Interface Segregation Principle)
*«Клиент не должен зависеть от интерфейсов, которые он не использует».*

**Ситуация:**
У вас есть сервис, который шлет уведомления. Стандартная реализация требует реализовать огромный интерфейс `NotificationInterface`, который включает методы `sendEmail()`, `sendSms()`, `sendPush()`, `sendTelegram()`.

Если вы пишете класс `EmailNotifier`, вам формально нужно реализовать `sendSms()` и `sendPush()`, хотя для email они не нужны. Вы пишете «пустые» реализации (no-ops), чтобы удовлетворить интерфейс.

**Оправданное нарушение:**
Вы игнорируете ISP и создаете **единый широкий интерфейс** `NotifierInterface`, который знает про все каналы, или вообще используете **конвенцию** (например, класс `EmailNotifier` просто имеет метод `send()`, без интерфейса).

**Почему это оправдано?**
1.  **Простота:** Не нужно думать о том, какой «микс» методов нужен конкретному уведомителю.
2.  **Меньше кода:** Нет необходимости создавать кучу маленьких интерфейсов (`EmailInterface`, `SmsInterface`), которые затем нужно как-то комбинировать.
3.  **Контекст:** В небольших системах количество каналов уведомлений ограничено (2–3 штуки). Гибкость, которую дает строгое ISP (создание отдельных интерфейсов для каждого метода), здесь избыточна.

**Код:**
```php
// Вместо:
// interface EmailNotifier { public function send(): void; }
// interface SmsNotifier { public function send(): void; }
// interface PushNotifier { public function send(): void; }

// Мы делаем так (нарушение ISP, так как "универсальный" метод send() у всех разный):
interface Notifier
{
    public function send(Notifiable $notification): void;
}

class EmailNotifier implements Notifier
{
    public function send(Notifiable $notification): void
    {
        // Логика отправки email
    }
    // Нет методов sendSms(), sendPush() — это нормально, так как 
    // мы работаем через общий контракт "отправить уведомление"
}
```
*Примечание: Строгий пуритан сказал бы: "Сделай интерфейс `EmailChannel` и `SmsChannel`". Но в реальности, если каналов мало, общий интерфейс проще.*

---

### Пример 2: Нарушение SRP (Single Responsibility Principle)
*«У класса должна быть одна причина для изменения».*

**Ситуация:**
Класс `User` (или `UserProfile`), который содержит не только данные (имя, email, аватар), но и методы для их валидации или даже форматирования для отображения.

Строгий SRP гласит:
*   `User` — только данные.
*   `UserValidator` — только валидация.
*   `UserView` — только форматирование.

**Оправданное нарушение:**
Вы оставляет методы `getEmailLowercased()` или `isEmailValid()` внутри класса `User`.

**Почему это оправдано?**
1.  **Связность:** Эти методы тесно связаны с данными пользователя. Выделение их в отдельный класс `UserHelper` или `UserFormatter` требует передавать туда объект `User` в каждом вызове, что делает код более "рыбным" (verbose).
2.  **Инкапсуляция:** Логика "как получить email" принадлежит самому `User`.
3.  **Ментальная нагрузка:** Разработчик ожидает, что методы, работающие с полем класса, находятся в этом классе. Поиск валидатора email в другом файле — лишний шаг.

**Код:**
```php
class User
{
    public function __construct(
        private string $name,
        private string $email
    ) {}

    public function getName(): string {
        return $this->name;
    }

    // "Нарушение" SRP: валидация и форматирование внутри "модели"
    public function isEmailValid(): bool 
    {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function getNormalizedEmail(): string 
    {
        return strtolower(trim($this->email));
    }
}
```
*В энтерпрайзе с DDD это может быть оправдано, если `User` — это Entity, а валидация — часть инвариантов сущности.*

---

### Пример 3: Нарушение D (Dependency Inversion Principle)
*«Высокоуровневые модули не должны зависеть от низкоуровневых. Оба должны зависеть от абстракций».*

**Ситуация:**
У вас есть простой скрипт для генерации отчета. Он читает данные из файла `CSV` и сохраняет в `PDF`.

Строгий D гласит:
1.  Создать интерфейс `ReportGenerator`.
2.  Создать интерфейс `DataSource`.
3.  Создать интерфейс `ReportSaver`.
4.  Создать классы `CsvDataSource`, `PdfSaver`.
5.  Инжектить их в `ReportGenerator` через DI-контейнер.

**Оправданное нарушение:**
Вы создаете один класс `ReportGenerator`, который внутри себя **сам** читает CSV и сам пишет PDF, получая пути к файлам через конструктор.

**Почему это оправдано?**
1.  **Масштаб:** Если этот код будет использоваться только в одном месте и один раз, создание 3–4 интерфейсов и 5 классов — это **оверхед** (overhead).
2.  **Сложность:** DI-контейнер становится сложнее, настройки сложнее, отладка сложнее.
3.  **Частота изменений:** Если логика генерации отчета не меняется часто, а источники данных фиксированы, абстракции не нужны. Они нужны там, где мы ожидаем *множественных* реализаций и *частых* замен.

**Код (Pragmatic Approach):**
```php
class ReportGenerator
{
    public function generate(string $csvPath, string $pdfPath): void
    {
        // 1. Читает CSV (низкоуровневая деталь)
        $data = $this->readCsv($csvPath);

        // 2. Обрабатывает данные (высокоуровневая логика)
        $summary = $this->calculateSummary($data);

        // 3. Сохраняет в PDF (низкоуровневая деталь)
        $this->saveToPdf($pdfPath, $summary);
    }

    private function readCsv(string $path): array { /* ... */ }
    private function saveToPdf(string $path, array $data): void { /* ... */ }
}
```
*Здесь нет интерфейсов. Есть прямой код. Для внутренней утилиты или CLI-скрипта это идеальное решение.*

---

### Когда же SOLID **обязательно** нужно соблюдать?

1.  **Когда код меняется часто.**
2.  **Когда над ним работают несколько человек.**
3.  **Когда появляются новые реализации** (новый платежный провайдер, новый тип отчета).
4.  **Когда нужен юнит-тестинг** (нужно мокнуть зависимости).

### Резюме

*   **SOLID** — это инструменты для борьбы с хаосом в больших, меняющихся системах.
*   **Нарушение SOLID** — это плата за скорость и простоту в маленьких или статичных частях системы.

**Лучшая практика:**
Сначала напишите код просто и эффективно (наругавшись на SOLID).
Если он начинает расти, дробиться, меняться — **тогда** применяйте SOLID для рефакторинга.
Не применяйте SOLID превентивно, "на всякий случай".

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

Да, можно. Наследование (Class Inheritance) — это лишь один из способов достижения полиморфизма. В современном PHP и чистом коде часто используются другие механизмы, которые делают код более гибким и менее «жестким».

Вот 4 основных способа реализовать принцип Open/Closed (OCP) без использования `extends`:

### 1. Композиция через Интерфейсы (Interface + Composition)
Это классический и самый правильный подход. Вместо того чтобы наследовать поведение, мы «складываем» его из частей, которые реализуют интерфейс.

**Суть:** Класс не знает, кто его вызвал, он знает только, что у объекта есть метод `calculate()`.

```php
interface Strategy 
{
    public function execute(): void;
}

// Реализации (могут быть в разных местах кодовой базы, даже в разных модулях)
class StrategyA implements Strategy {
    public function execute(): void { /* ... */ }
}
class StrategyB implements Strategy {
    public function execute(): void { /* ... */ }
}

// Клиент зависит от интерфейса
class Worker {
    public function __construct(private Strategy $strategy) {}
    
    public function run(): void {
        $this->strategy->execute(); 
    }
}

// Расширение: Добавляем StrategyC.
// Worker менять не нужно! Нужно просто создать new Worker(new StrategyC());
```
**Почему это лучше наследования?**
*   Нет «слепых» наследников.
*   Легко менять реализацию в рантайме (например, переключить стратегию на лету).
*   Соответствует принципу «Составляй, а не наследуй» (Compose, don't inherit).

---

### 2. Паттерн «Посетитель» (Visitor Pattern)
Идеален, когда у вас есть **множество разных типов объектов**, и вы хотите добавлять к ним **новые операции** без изменения самих объектов.

**Суть:** Операции выносятся в отдельный класс-«посетитель». Объекты принимают посетителя и вызывают на нем метод `visit()`, который специфичен для этого типа.

```php
interface Shape {
    public function accept(ShapeVisitor $visitor);
}

interface ShapeVisitor {
    public function visitCircle(Circle $circle): void;
    public function visitSquare(Square $square): void;
    // Чтобы добавить новый тип (Треугольник), нужно добавить метод здесь
    public function visitTriangle(Triangle $triangle): void; 
}

class Circle implements Shape {
    public function accept(ShapeVisitor $visitor): void {
        $visitor->visitCircle($this);
    }
}

// Клиент
class AreaCalculator implements ShapeVisitor {
    public function visitCircle(Circle $c): void { echo 3.14 * $c->r * $c->r; }
    public function visitSquare(Square $s): void { echo $s->side * $s->side; }
    public function visitTriangle(Triangle $t): void { echo 0.5 * $t->base * $t->height; }
}

// Расширение: Добавили новую операцию "PerimeterCalculator"
// Классы Circle, Square менять не нужно! Нужно создать новый Visitor.
```
**Почему это OCP?**
*   Чтобы добавить **новый тип фигуры** — создаем класс `Circle`, реализуем `accept`. Старые `Visitor`'ы менять не нужно (если они уже знают про этот тип, но обычно Visitor знает про все типы заранее).
*   Чтобы добавить **новую операцию** (например, расчет площади) — создаем новый `Visitor`. Классы фигур менять **не нужно**!

*Нюанс:* Visitor хорошо расширяется для **новых операций**, но плохо для **новых типов объектов** (приходится менять интерфейс Visitor). Но в 80% случаев операций больше, чем типов.

---

### 3. Реестр/Фабрика + Ключи (Registry / Strategy Map)
Это прагматичный подход, часто используемый в Symfony, Laravel и других фреймворках. Вместо жесткой привязки к классу, мы используем **ключ** (строку или enum) для выбора реализации.

**Суть:** Есть массив (или DI-контейнер), где ключ — это тип/название, а значение — callable, объект или класс.

```php
class PaymentProcessor {
    /**
     * @var array<string, callable>
     */
    private array $handlers = [];

    public function register(string $method, callable $handler): void
    {
        $this->handlers[$method] = $handler;
    }

    public function process(string $method, array $data): void
    {
        if (!isset($this->handlers[$method])) {
            throw new \Exception("No handler for $method");
        }

        // Вызываем переданный callback
        call_user_func($this->handlers[$method], $data);
    }
}

// Регистрация (в bootstrap или конфиге)
$processor = new PaymentProcessor();
$processor->register('visa', fn(array $d) => echo "Visa payment: " . $d['amount']);
$processor->register('mastercard', fn(array $d) => echo "MC payment: " . $d['amount']);

// 🆕 Расширение: Добавляем PayPay
// process() менять не нужно!
$processor->register('paypal', fn(array $d) => echo "PayPal: " . $d['amount']);
```
**Почему это OCP?**
*   Класс `PaymentProcessor` не знает о конкретных типах платежей.
*   Новые типы добавляются через `register()`, без изменения исходного кода класса-обрабатчика.
*   Это особенно удобно, если реализация — это просто функция (closure) или класс из сторонней библиотеки.

---

### 4. Декораторы (Decorators)
Если нужно **модифицировать** поведение существующего объекта, не изменяя его исходный код.

**Суть:** Оборачиваем объект в другой, который наследует от того же интерфейса, но добавляет доп. логику (логирование, кэширование, транзакции) до или после вызова.

```php
interface Logger {
    public function log(string $message): void;
}

class ConsoleLogger implements Logger {
    public function log(string $message): void {
        echo $message . "\n";
    }
}

// Декоратор: Добавляет таймстамп, не меняя ConsoleLogger
class TimestampedLogger implements Logger {
    public function __construct(private Logger $logger) {}

    public function log(string $message): void {
        $timestamp = date('Y-m-d H:i:s');
        $this->logger->log("[$timestamp] $message");
    }
}

// Использование
$baseLogger = new ConsoleLogger();
$finalLogger = new TimestampedLogger($baseLogger);

// 🆕 Расширение: Добавим KibanaLogger (декоратор)
// ConsoleLogger и TimestampedLogger менять не нужно!
```
**Почему это OCP?**
*   Мы расширяем функциональность (добавляем таймстампы, логирование в файл, шифрование) без изменения исходного класса.
*   Декораторы можно накладывать друг на друга (цепочка).

---

### Сравнение подходов

| Метод | Когда использовать | Плюсы | Минусы |
| :--- | :--- | :--- | :--- |
| **Наследование** | Когда есть строгая иерархия «is-a» (Например, `Animal` -> `Dog`) | Простая иерархия, автополиморфизм | Жесткая связь, «проблема множественного наследования», трудно тестировать |
| **Интерфейсы + Композиция** | **Универсальный выбор**. Когда поведение меняется независимо от типа данных | Гибкость, легкость мока, отсутствие «цепочек наследования» | Нужно явно внедрять зависимости |
| **Реестр/Фабрика** | Когда реализации динамические, или их много, или они приходят извне | Максимальная гибкость, легко добавлять/удалять реализации | Потеря статической типизации (если используется стринг-ключи), сложнее отладка |
| **Декораторы** | Нужно добавить поведенческие аспекты (логирование, кэш, транзакции) | Чистое разделение ответственности | Могут создавать «глубокие» вложенности объектов |
| **Visitor** | Много объектов, много операций над ними | Легко добавлять новые операции | Сложность кода, «разрыв» инкапсуляции |

### Итог

**Наследование — это не единственный путь к OCP.**
В большинстве современных PHP-приложений (Symfony, Laravel) вы увидите:
1.  **Интерфейсы** для основных сервисов (репозитории, сентенцы, гейты).
2.  **Тегирование в DI-контейнере** (например, `eventSubscriber`, `listener`) — это по сути реализация «Реестра».
3.  **Служебные функции** (closures) для простейших стратегий.

Выбор зависит от контекста:
*   Нужна гибкость и замена реализаций? → **Интерфейсы/Композиция**.
*   Нужны динамические обработчики? → **Реестр/Тегирование**.
*   Нужно расширять поведение? → **Декораторы**.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

Нет, **SRP (Single Responsibility Principle) не ограничивается только классами**. Он является фундаментальным принципом проектирования, который применим к **любому модулю кода**, независимо от его размера или типа.

Вот к каким сущностям он применим:

### 1. Функции и методы
Это самый частый уровень нарушения SRP.
*   **Правильно:** Функция делает одну вещь (например, `validateEmail(string $email): bool`).
*   **Нарушение:** Функция `processOrder()` внутри себя:
    1.  Читает данные из БД.
    2.  Валидирует данные.
    3.  Рассчитывает цену.
    4.  Отправляет письмо.
    5.  Сохраняет в БД.
    
    Если изменится способ отправки писем, вам нужно менять `processOrder()`. Это плохая идея. Лучше вынести отправку в отдельную функцию или сервис.

### 2. Модули и пространства имен (Namespaces)
*   **Правильно:** Модуль `Users` содержит только логику, связанную с пользователями (регистрация, профиль, права доступа).
*   **Нарушение:** В модуле `Users` лежит класс `InvoiceGenerator`, потому что «так удобно было написать». Если изменится формат счетов, вы меняете модуль пользователей, хотя пользователи и счета — разные бизнес-концепты.

### 3. Микросервисы
*   **Правильно:** Сервис `Order-Service` отвечает за жизненный цикл заказа. Сервис `Payment-Service` отвечает за оплату.
*   **Нарушение:** Сервис `Order-Service` также содержит логику отправки SMS, генерации PDF-отчетов и синхронизации с CRM. Теперь эти три независимые области ответственности связаны одной точкой отказа и одним циклом развертывания.

### 4. Конфигурационные файлы и скрипты
*   **Правильно:** Файл `database.yml` содержит только настройки БД.
*   **Нарушение:** В `database.yml` также лежат ключи API для Google Maps и настройки почтового сервера. Изменение почтовых настроек требует трогать файл базы данных.

---

### Важный нюанс: «Одна ответственность» — это не «один метод» или «один объект»

Частая ошибка новичков — интерпретировать SRP как «у класса должен быть только один метод» или «каждый объект должен быть минимальным».

**SRP означает: «У модуля должна быть только одна причина для изменения».**

Давайте посмотрим на пример, который может показаться нарушением SRP, но на самом деле им не является:

```php
class User
{
    private string $email;
    private string $name;

    // Ответственность: Представлять пользователя
    public function getEmail(): string { return $this->email; }
    public function getName(): string { return $this->name; }

    // Ответственность: Валидировать себя
    public function isEmailValid(): bool {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
```

**Есть ли здесь нарушение SRP?**
Нет. Почему?
1.  Если изменится формат валидации email (например, добавится требование к домену), мы меняем `User::isEmailValid()`.
2.  Если изменится структура данных пользователя (добавится поле `phone`), мы меняем `User`.
3.  Но обе эти причины изменения **относятся к одной концепции**: «что такое пользователь и как он проверяется».

А вот если мы добавим метод `sendEmailNotification()` в класс `User`:

```php
class User
{
    // ... предыдущие методы ...

    // Новая ответственность: Отправка уведомлений
    public function sendEmailNotification(string $message): void {
        // Логика работы с SMTP, шаблонами, очередью...
    }
}
```

Теперь у класса `User` **две причины для изменения**:
1.  Изменились правила валидации email или структура данных.
2.  Изменился провайдер SMTP, формат писем или логика отправки.

Это **нарушение SRP**. Метод отправки уведомлений должен быть вынесен в отдельный сервис `NotificationSender`.

---

### Как определить, нарушен ли SRP? (Вопрос «Почему изменится?»)

Задайте себе вопрос: **«Если завтра изменится бизнес-требование X, какой код мне придется переписывать?»**

*   Если один класс меняется по 5 разным причинам (изменились правила валидации, изменился API банка, изменился формат лога, изменилась политика конфиденциальности, изменилась структура БД) — значит, в нем смешаны 5 разных ответственностей.
*   Если класс меняется только по одной причине (например, изменились правила валидации заказов) — значит, SRP соблюдено.

### Итог

SRP — это про **когерентность ответственности**.
Применяйте его к:
*   Классам
*   Методам
*   Файлам
*   Модулям
*   Микросервисам
*   Даже к переменным в некоторых случаях (если одна переменная несет в себе два разных состояния, это может быть признаком плохого дизайна).

Главный критерий: **Сколько независимых причин для изменения у этого модуля?** Если больше одной — разбейте.

> ⏱ 23.2s · prompt 7110 tok · output 1753 tok

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

**Кандидат:**

Контейнер внедрения зависимостей (Dependency Injection Container, DI-контейнер) в Symfony — это один из самых мощных, но и самых недооцененных механизмов фреймворка. Чтобы понять его работу, нужно разделить процесс на три фазы: **Компиляция** (build time), **Автоинжект** (runtime, ленивое создание) и **Запрос** (runtime, получение готового объекта).

Давайте разберем каждую фазу детально.

---

### 1. Фаза Компиляции (Compile Time)

Это происходит один раз, при выполнении команды `php bin/console cache:clear` или `php bin/console cache:warmup`. Результатом является файл `ContainerXXXX.php` в кэше (например, `var/cache/dev/ContainerXXXX/`).

Что делает Symfony в этот момент?

1.  **Оптимизация графа зависимостей:**
    *   Symfony строит DAG (Directed Acyclic Graph) — направленный ациклический граф — всех сервисов.
    *   Он определяет, кто от кого зависит.
    *   Он удаляет «мертвый код»: если сервис объявлен в конфиге, но ни один другой сервис (или контроллер, или форма) не зависит от него, Symfony может оптимизировать его создание или даже удалить, если это разрешено конфигурацией.

2.  **Генерация кода:**
    Вместо того чтобы хранить конфиг в YAML/PHP-массиве и парсить его каждый раз при запросе, Symfony **генерирует PHP-код**.
    *   Создается корневой класс `Container`.
    *   Для каждого сервиса генерируется метод (или фабрика), который знает, как создать этот объект.
    *   Если сервис зависит от других, их методы вызываются внутри этого метода.

    *Пример сгенерированного псевдокода:*
    ```php
    // Внутри класса ContainerXXXX
    public function getMessenger_Messenger_MessengerService()
    {
        $a = $this->get('logger')->channel('messenger'); // Получаем логгер
        $b = new \Symfony\Component\Messenger\Handler\HandlersLocator(
            $this->getHandlers() // Получаем массив хэндлеров
        );
        
        return new \Symfony\Component\Messenger\Transport\AmqpExt\AmqpTransport(
            'amqp://localhost:5672/%2F', 
            $b, 
            $a
        );
    }
    ```

3.  **Определение «Ленивых» сервисов (Lazy Services):**
    *   Если сервис тяжеловесный (например, подключается к БД, Redis или внешнему API), Symfony помечает его как `lazy`.
    *   Вместо создания объекта сразу, контейнер создает **Proxy-объект** (стандартный PHP-proxy или Symfony-специфичный).
    *   Proxy содержит только метаинформацию (класс, методы) и лениво загружает реальную реализацию только тогда, когда на него вызывается метод.

**Итог фазы 1:** Вместо анализа YAML-файлов во время запроса, у нас есть оптимизированный PHP-класс, который знает, как собрать объект. Это быстрее на порядки.

---

### 2. Фаза Autowiring (Автоинжект)

Autowiring — это механизм, позволяющий не указывать явным образом, какой сервис нужно внедрить в конструктор, если можно определить его по **типу**.

Как это работает?

1.  **Правило по умолчанию:**
    *   Если в конструкторе метода или класса указан интерфейс или класс (например, `EntityManagerInterface $em`), Symfony пытается найти сервис, который реализует этот интерфейс.
    *   Если находит **ровно один** такой сервис — он внедряется автоматически.
    *   Если находит **несколько** — возникает ошибка `MultipleServicesFoundException`. Тогда нужно явно указать тег или имя сервиса.

2.  **Использование тегов (`autoconfigure`):**
    *   В Symfony 5+ все сервисы, которые реализуют известные интерфейсы (например, `KernelEventSubscriber`, `EventSubscriberInterface`), автоматически получают соответствующие теги, если включено `autoconfigure`.
    *   Например, если ваш класс реализует `EventSubscriberInterface`, Symfony автоматически добавляет ему тег `kernel.event_subscriber`, и ядро фреймворка сможет подписать его на события, даже если вы не писали это в конфиге.

3.  **Исключения:**
    *   `private` сервисы (по умолчанию в Symfony все сервисы `private`): Они доступны только тем, кто явно зависит от них.
    *   `public` сервисы: Доступны из любого места через `$container->get()` (редко используется в бизнес-логике, чаще в CLI-скриптах).

**Итог фазы 2:** Autowiring снижает количество конфигурационного кода. Мы описываем *что* нам нужно (тип), а не *где* это взять (имя сервиса).

---

### 3. Фаза Запроса (Runtime: Когда сервис запрошен впервые)

Допустим, пользователь сделал запрос к контроллеру, который использует сервис `OrderService`. `OrderService` зависит от `PaymentGateway`.

#### Шаг 1: Контроллер запрашивает сервис
```php
class OrderController extends AbstractController
{
    public function create(OrderService $orderService): Response
    {
        $order = $orderService->createNewOrder();
        // ...
    }
}
```
Symfony вызывает метод `OrderController::create()`. Перед этим он должен создать объект `OrderService`.

#### Шаг 2: Контейнер ищет или создает `OrderService`
1.  Контейнер смотрит в свою внутреннюю хеш-таблицу (или метод `has('order_service')`).
2.  Если сервис уже создан (singleton по умолчанию) — возвращается готовый объект.
3.  Если нет — вызывается метод создания (сгенерированный в фазе 1).

#### Шаг 3: Создание зависимостей (Recurse)
Чтобы создать `OrderService`, контейнер должен сначала создать его зависимости: `PaymentGateway` и `Logger`.

1.  Контейнер вызывает `getPaymentGateway()`.
    *   Если `PaymentGateway` — ленивый: Создается Proxy.
    *   Если нет: Создается реальное соединение с внешним API.
2.  Контейнер вызывает `getLogger()`.
    *   Возвращается стандартный логгер.
3.  Теперь у контейнера есть все зависимости. Он вызывает `new OrderService($paymentGateway, $logger)`.

#### Шаг 4: Внедрение и кэширование
1.  Объект `OrderService` сохраняется в память контейнера (или в кэш, если он `public`).
2.  Контроллер получает готовый объект `$orderService`.

**Важный момент: Ленивая инициализация (Lazy Loading)**
Если `OrderService` имеет метод `sendEmail()`, который зависит от тяжелого `EmailSender`, а в данном запросе мы только читаем заказ:
*   `EmailSender` **не будет создан**, пока метод `sendEmail()` не будет вызван.
*   Контейнер внедряет Proxy в `OrderService`.
*   Когда вы вызываете `$orderService->sendEmail()`, Proxy перехватывает вызов, запрашивает у контейнера настоящий `EmailSender` и проксирует метод.

---

### Как устроен DI-контейнер под капотом? (Технические детали)

1.  **Иерархия контейнеров:**
    *   **Main Container:** Основной контейнер приложения.
    *   **Child Containers:** Иногда используются для изоляции (например, в отдельных микросервисах или в Symfony с использованием `bundle` с изолированным контекстом).
    *   В большинстве случаев есть один корневой контейнер.

2.  **Scope (Область видимости):**
    *   `prototype`: Каждый вызов `get()` создает **новый** объект. (Редко используется, медленно).
    *   `singleton` (по умолчанию): Объект создается один раз и переиспользуется.

3.  **Tags (Теги):**
    *   Теги не влияют на создание объекта напрямую, но используются для **рефлексии** и **реестров**.
    *   Например, все сервисы с тегом `monolog.logger` собираются в один объект `MonologChannelLogger`, который знает, куда писать логи для разных каналов.

4.  **Decorators (Декораторы):**
    *   Symfony позволяет «обернуть» сервис.
    *   Пример: Вы хотите логировать все вызовы внешнего API.
    *   Вы создаете `LoggingApiDecorator`, который зависит от `ApiInterface`.
    *   В конфиге: `decorates: 'api_client'`.
    *   Теперь, когда кто-то запрашивает `api_client`, он получает `LoggingApiDecorator`, а не оригинальный клиент. Оригинальный клиент доступен внутри декоратора.

---

### Типичные проблемы и решения

| Проблема | Причина | Решение |
| :--- | :--- | :--- |
| `ServiceNotFoundException` | Сервис не зарегистрирован или `private`, а вы пытаетесь получить его из вне. | Проверьте конфиг. Используйте `Autowiring` или явно укажите `public: true` (если нужно). |
| `MultipleServicesFoundException` | Несколько сервисов реализуют один интерфейс. | Укажите конкретный сервис через тег или переменную `default_service` (в Symfony 6+ есть возможность задать `default_autowiring_service`). |
| `CircularReference` | Сервис A зависит от B, B зависит от A. | Разбейте циклическую зависимость. Используйте события (`EventDispatcher`) или вынесите общую логику в третий сервис C, от которого зависят A и B. |
| Медленный первый запрос | Создание тяжелых зависимостей. | Используйте `lazy: true` для тяжелых сервисов. |

---

### Итог

1.  **Компиляция:** Symfony превращает конфиг в оптимизированный PHP-код, строит граф зависимостей и определяет ленивые сервисы.
2.  **Autowiring:** Автоматически сопоставляет типы с сервисами, снижая потребность в ручной конфигурации.
3.  **Запрос:** Контейнер создает объект по цепочке (зависимости сначала), кэширует его (singleton) и возвращает вызывающему.
4.  **Ленивость:** Тяжелые зависимости откладываются до момента их фактического использования, используя Proxy.

Это делает Symfony одним из самых производительных PHP-фреймворков: конфигурация анализируется один раз, а во время выполнения происходит лишь быстрая загрузка PHP-классов и вызов методов.

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

Циклические зависимости — это «рак» архитектуры. Если два сервиса жестко зависят друг от друга через конструктор (`A` инжектит `B`, а `B` инжектит `A`), контейнер не может создать ни один из них, потому что для создания первого нужен второй, а для второго — первый.

В Symfony (и в большинстве DI-контейнеров) есть несколько способов разрулить эту ситуацию, от «костылей» до правильных архитектурных решений.

---

### 1. Самый правильный способ: Инверсия зависимостей через События (Event Dispatcher)

Это золотой стандарт в Symfony. Смысл: `A` не знает о `B`. `A` просто говорит миру: «Произошло событие X». `B` подписан на это событие и делает свою работу.

**Ситуация:**
*   `OrderService` должен обновить инвентарь.
*   `InventoryService` должен изменить статус заказа (например, на «выполнен», если товара нет).

**Было (цикл):**
```php
class OrderService {
    public function __construct(private InventoryService $inventory) {} 
    // ...
    public function placeOrder(): void {
        $this->inventory->decreaseStock();
    }
}

class InventoryService {
    public function __construct(private OrderService $order) {} 
    // ...
    public function decreaseStock(): void {
        $this->order->markAsFulfilled();
    }
}
```

**Стало (через события):**
1.  **OrderService** генерирует событие `OrderPlacedEvent`. Он не знает про инвентарь вообще.
2.  **InventoryService** слушает это событие и уменьшает сток.
3.  Если инвентарь решает, что нужно изменить статус, он генерирует *другое* событие `StockUpdatedEvent`.
4.  **OrderService** (или отдельный `OrderStatusListener`) слушает это событие и меняет статус.

**Код:**
```php
// 1. Событие
class OrderPlacedEvent {
    public function __construct(public Order $order) {}
}

// 2. Издатель (Publisher)
class OrderService {
    public function __construct(private EventDispatcherInterface $dispatcher) {}

    public function placeOrder(): void {
        // Логика...
        $this->dispatcher->dispatch(new OrderPlacedEvent($order));
    }
}

// 3. Слушатель (Listener) - теперь зависит только от репозитория, а не от OrderService
class InventoryListener {
    public function __construct(private InventoryRepository $repo) {}

    public function onOrderPlaced(OrderPlacedEvent $event): void {
        $event->order->getProduct()->decreaseStock();
        $this->repo->save($event->order->getProduct());
        
        // Если нужно сообщить обратно:
        // $this->dispatcher->dispatch(new StockUpdatedEvent(...));
    }
}
```
**Плюсы:**
*   Полная декомпозиция.
*   Легко добавлять новые слушатели (аудит, аналитика) без изменения основного кода.
*   Нарушение OCP соблюдается (открыто для расширения, закрыто для модификации).

---

### 2. Способ: Общий сервис-посредник (Mediator / Service Locator局部)

Если логика `A` и `B` действительно тесно связана и разделение на события кажется избыточным (например, это одна транзакция), создайте третий сервис `C`, который содержит логику, которой не хватает обоим.

**Ситуация:**
*   `A` нужно вычислить значение.
*   `B` нужно сохранить это значение.
*   `A` нужен `B` для сохранения.
*   `B` нужен `A` для получения значения.

**Решение:**
Создайте сервис `Calculator` (или `Helper`), который знает и то, и другое.
*   `A` зависит от `Calculator`.
*   `B` зависит от `Calculator`.
*   `Calculator` зависит от ничего (или от репозитория).

Так вы убираете зависимость `A -> B` и `B -> A`, заменив их на `A -> C` и `B -> C`.

---

### 3. Способ: `Lazy` сервисы (Ленивая инициализация)

Если циклическая зависимость возникла из-за того, что один метод `A` вызывает метод `B`, а в 99% случаев этот метод не используется, можно сделать `B` ленивым.

В Symfony это делается через интерфейс `ServiceSubscriberInterface` или тег `kernel.controller` (для контроллеров), но чаще всего используют **Proxy-генерацию**.

Если вы объявите один из сервисов как `lazy: true`, DI-контейнер не будет создавать его сразу. Он создаст «заглушку» (Proxy). Циклическая зависимость разрывается, потому что `A` получает не настоящий `B`, а его обертку. Реальный `B` создастся только тогда, когда вы вызовете его метод.

**Конфиг (`services.yaml`):**
```yaml
services:
    App\Service\A:
        lazy: true # Теперь A не требует B при старте, а B может требовать A (который уже есть как прокси? Нет, обычно ленивым делают тот, кто втянут в цикл)
        
    App\Service\B:
        lazy: true
```
*Примечание:* Это работает, но это **костыль**. Он скрывает проблему архитектуры. Используйте его только если вы уверены, что логика действительно независима и не требует атомарности.

---

### 4. Способ: Внедрение через метод (Setter Injection) вместо Конструктора

Это нарушение чистой архитектуры (Dependency Injection), но иногда спасает положение.

Вместо:
```php
class A {
    public function __construct(private B $b) {}
}
```
Делаем:
```php
class A {
    private ?B $b;
    
    public function setB(B $b): void {
        $this->b = $b;
    }
}
```

Теперь `A` не зависит от `B` в момент создания. `B` может зависеть от `A`. После создания обоих сервисов, мы вручную (или через listener) вызываем `$a->setB($b)`.

**Минусы:**
*   Нарушение инкапсуляции.
*   Состояние объекта меняется после создания (он больше не immutable).
*   Трудно тестировать (надо помнить, что надо вызвать `setB`).

Используйте это только в крайнем случае.

---

### 5. Способ: `service_locator` (Локальный доступ)

Если `A` редко вызывает `B`, можно не инжектить `B` в конструктор `A`, а достать его из контейнера в нужный момент.

```php
class A {
    public function __construct(private ContainerInterface $container) {}

    public function doSomething(): void {
        $b = $this->container->get(B::class); // Ленивое получение
        $b->doWork();
    }
}
```
Это позволяет избежать циклической зависимости при создании, потому что `A` зависит только от `Container`, а не от `B`.

**Минусы:**
*   Трудно мокать в юнит-тестах (приходится мокать Container).
*   Нарушение SOLID (SRP/DIP), так как `A` теперь знает о наличии `B` в системе.

---

### Как выбрать?

1.  **Сначала спросите себя:** «Должны ли эти два сервиса быть в одной транзакции? Должны ли они знать друг о друге?»
    *   Если **Да** и они делают разные вещи — это **События** (Пункт 1).
    *   Если **Нет** и они делают общую работу — это **Общий сервис** (Пункт 2).

2.  **Если нужно быстро починить баг:** Используйте **Lazy** (Пункт 3) или **Setter Injection** (Пункт 4).

3.  **Если зависимость редкая:** Используйте **Service Locator** (Пункт 5).

### Чего делать НЕ стоит:
*   Делать оба сервиса `public` и гонять их через `static` методы.
*   Использовать `@var` и `static::instance()` (глобальные переменные).
*   Прятать логик в методы-фабрики, которые создают объекты внутри себя.

**Мой совет:** В 90% случаев в Symfony циклические зависимости лечатся переносом логики в **Event Subscriber**. Это самый «симфоничный» и чистый способ.

> ⏱ 32.6s · prompt 14346 tok · output 2446 tok

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

**Кандидат:**

Это классическая задача **асинхронной обработки задач** (Asynchronous Job Processing). В Symfony есть несколько способов решить её, каждый со своими преимуществами и недостатками.

Ключевой принцип: **Разделение ответственности**. Ответ пользователю должен возвращаться мгновенно, а «тяжелые» или «внешние» задачи (почта, поиск) выполняться позже.

Вот 4 основных подхода, от самого простого к самому enterprise.

---

### Вариант 1: Symfony Messenger + Doctrine DB Transport (Рекомендуемый стандарт)

Это встроенное решение Symfony, которое превращает метод в «сообщение», которое ставится в очередь.

#### Как это работает:
1.  Вы создаете класс-сообщение (DTO), например `UserRegisteredMessage`.
2.  В сервисе регистрации вы диспатчите это сообщение: `$this->bus->dispatch(new UserRegisteredMessage($user))`.
3.  Symfony сериализует сообщение и кладет его в **БД** (таблица `messenger_messages`), если вы настроили транспорт `doctrine`.
4.  Запускается воркер (CLI-процесс), который постоянно опрашивает БД, берет сообщения и вызывает хендлеры.
5.  Хендлеры выполняют логику: `EmailSender->send()` и `SearchIndexer->index()`.

#### Код:
```php
// 1. Сообщение (Payload)
class UserRegisteredMessage {
    public function __construct(public int $userId) {}
}

// 2. Хендлеры (Workers)
class SendWelcomeEmailHandler {
    public function __invoke(UserRegisteredMessage $message): void {
        $user = ... // загрузка пользователя
        $this->mailer->send($user);
    }
}

class UpdateSearchIndexHandler {
    public function __invoke(UserRegisteredMessage $message): void {
        $user = ...
        $this->elasticsearch->index($user);
    }
}
```

#### Запуск воркера (в Docker Compose или systemd):
```bash
php bin/console messenger:consume async --limit 50
```

#### Trade-offs:
*   **Плюсы:**
    *   **Простота:** Использует стандартный стек Symfony.
    *   **Надежность:** Если сервер упадет, сообщения остаются в БД.
    *   **Логирование:** Встроенное отслеживание статусов сообщений (pending, processed, failed).
    *   **Retries:** Автоматический повтор при ошибке (настраивается).
*   **Минусы:**
    *   **Задержка:** Воркер опрашивает БД раз в N секунд. Если задача критична к времени (секунды), это может быть медленно.
    *   **Простые операции:** Для простой отправки письма это может быть «перегруз» (создание строки в БД, сериализация).

---

### Вариант 2: Symfony Messenger + AMQP/RabbitMQ/Kafka Transport

Тот же самый интерфейс, но вместо БД используется специализированный брокер очередей.

#### Как это работает:
Архитектура такая же, как в Варианте 1, но транспорт в `framework.yaml` меняется на `amqp` или `kafka`. Сообщения не хранятся в БД, а сразу уходят в память брокера.

#### Trade-offs:
*   **Плюсы:**
    *   **Высокая производительность:** Миллионы сообщений в секунду.
    *   **Масштабируемость:** Легко добавить 10 воркеров на разных машинах.
    *   **Низкая задержка:** Почти мгновенное получение сообщения воркером.
    *   **Разделение забот:** БД не нагружается операциями записи очередей.
*   **Минусы:**
    *   **Сложность инфраструктуры:** Нужен отдельный сервис (RabbitMQ/Kafka) и его администрирование.
    *   **Сложность отладки:** Нужно смотреть логи брокера, а не только БД.
    *   **Переход:** Если у вас небольшая система, это избыточно.

---

### Вариант 3: Cron Job + «Колонки» в БД (Simple but Dull)

Если вы не хотите вводить Messenger или брокеры, можно использовать «костыль», который часто встречается в легаси-проектах.

#### Как это работает:
1.  В таблице `users` есть поле `pending_tasks` (JSON или отдельные битовые флаги: `email_sent = false`, `indexed = false`).
2.  После регистрации эти флаги ставятся в `false`.
3.  Запускается Cron-скрипт каждые 5 минут: `php bin/console app:process-pending-tasks`.
4.  Скрипт берет всех пользователей с `email_sent = false`, отправляет письма и ставит `true`.

#### Trade-offs:
*   **Плюсы:**
    *   **Максимальная простота:** Нет новых компонентов, только БД и Cron.
    *   **Легко отлаживать:** Видно состояние в БД.
*   **Минусы:**
    *   **Задержка:** До 5 минут (зависит от интервала Cron).
    *   **Масштабируемость:** Плохо. Если пользователей миллионы, один скрипт не справится.
    *   **Гибкость:** Чтобы добавить новую задачу (например, «отправить SMS»), нужно менять структуру БД и код скрипта.

---

### Вариант 4: Реактивное программирование / Stream Processing (Event-Driven)

Более современный подход, где события стримятся в реальном времени (например, через Redis Streams или NATS).

#### Как это работает:
1.  При регистрации публикуется событие в канал Redis.
2.  Сервис `EmailWorker` подписан на этот канал и мгновенно получает событие.
3.  Сервис `SearchWorker` тоже подписан.

#### Trade-offs:
*   **Плюсы:**
    *   **Реальное время:** Миллисекундная задержка.
    *   **Горизонтальное масштабирование:** Легко добавить воркеры.
    *   **Распутывание:** Сервисы полностью независимы.
*   **Минусы:**
    *   **Сложность:** Нужен менеджер очередей (Redis/NATS).
    *   **Идемпотентность:** Если событие доставлено дважды, воркеры должны уметь обрабатывать дубликаты (например, не дублировать письмо).

---

### Что выбрать? (Матрица решений)

| Критерий | DB Transport (Messenger) | AMQP/Kafka | Cron |
| :--- | :--- | :--- | :--- |
| **Задержка** | Секунды (polling) | Миллисекунды | Минуты |
| **Нагрузка на БД** | Высокая (записи) | Низкая (нет) | Средняя (чтение) |
| **Сложность** | Низкая | Высокая | Очень низкая |
| **Масштаб** | Средний | Высокий | Низкий |
| **Надежность** | Высокая (ACID) | Высокая (ack) | Средняя |
| **Для чего** | **Стандарт для большинства PHP-приложений** | Высоконагруженные системы, микросервисы | Простые скрипты, небольшие нагрузки |

---

### Детали реализации в Symfony (Best Practices)

Независимо от выбора транспорта, вот как правильно организовать код:

1.  **Идемпотентность Хендлеров:**
    Хендлер должен уметь обрабатывать повторное выполнение.
    ```php
    class SendWelcomeEmailHandler {
        public function __invoke(UserRegisteredMessage $message): void {
            $user = $this->userRepo->find($message->userId);
            
            // Проверка: уже отправляли?
            if ($user->getEmailSentAt() !== null) {
                return; // Просто выходим
            }

            $this->mailer->send($user);
            $user->markEmailSent();
            $this->userRepo->save($user);
        }
    }
    ```

2.  **Транзакционность:**
    Основной бизнес-процесс (регистрация) должен завершиться транзакцией *до* отправки сообщения в очередь.
    *   Если транзакция откатится — сообщение не должно уходить (иначе будет «письмо из ниоткуда»).
    *   Если сообщение ушло, а транзакция откатится — это хуже, но разрешимо (письмо отправится, хотя пользователь не зарегистрирован).
    *   *Решение:* В Symfony Messenger сообщения часто отправляются *после* коммита транзакции (можно настроить через `finalizers`).

3.  **Обработка ошибок:**
    *   Если письмо не ушло из-за ошибки SMTP, Messenger попытается повторить (настраивается `max_retries`).
    *   Если после 5 попыток не ушло — сообщение падает в `rejected` (или `dead-letter` queue).
    *   **Важно:** Не блокируйте основной поток ожидания из-за ошибки письма.

4.  **Логирование:**
    В каждом хендлере логируйте начало и конец работы. Это поможет отследить, где застряло письмо.

### Итоговая рекомендация

Для 95% Symfony-приложений:
1.  Используйте **Symfony Messenger**.
2.  Настройте транспорт **Doctrine** (БД) для простоты и надежности.
3.  Запустите воркер `messenger:consume` в фоне (Docker/systemd).
4.  Если нагрузка вырастет и БД станет узким местом — переключите транспорт на **RabbitMQ** или **Redis** без изменения кода бизнес-логики (только смена конфига).

Это даст вам асинхронность, надежность и простоту одновременно.

> ⏱ 31.1s · prompt 18884 tok · output 2285 tok

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

**Кандидат:**

Контроль доступа в Symfony построен на двух независимых, но взаимосвязанных механизмах:
1.  **Authenticator (Аутентификация):** Кто вы? (Проверка пароля, токена, OAuth).
2.  **Access Control (Авторизация):** Что вам разрешено делать? (Роли, Voters, аннотации/атрибуты).

В Symfony 5.4+ и 6/7 логика сильно упрощена благодаря атрибутам и стандартным провайдерам. Давайте разберем архитектуру.

---

### 1. Аутентификация: Как Symfony знает, кто вы?

В современной Symfony аутентификация работает через **Authentication Managers** и **Authenticators**.

#### Поток работы:
1.  **Запрос:** Пользователь делает HTTP-запрос.
2.  **Firewall (Ограждение):** Symfony определяет, какие «правила безопасности» применяются к этому URL (например, `/admin/*` требует логина, `/public/*` — нет).
3.  **Authentication Entry Point:** Если пользователь не аутентифицирован, а доступ требует этого, создается событие `AuthenticationException`, и вызывается Entry Point (например, редирект на страницу логина).
4.  **Authenticator:** Если запрос содержит данные для входа (форм-логин, JSON-токен, Cookie), Symfony вызывает соответствующий `Authenticator`.
    *   *Пример:* `FormLoginAuthenticator` проверяет POST-данные, ищет пользователя через `UserProvider`, сравнивает хеши паролей.
    *   *Пример:* `JWTAuthenticator` (из пакетов типа `lexik/jwt-authentication-bundle`) парсит заголовок `Authorization: Bearer <token>`, дешифрует JWT и находит пользователя.
5.  **User Provider:** Загружает объект `User` из БД (или другого хранилища).
6.  **Security Token:** Создается объект `Token`, который хранится в сессии или возвращается клиенту. Теперь запрос «знает», чей он.

> **Важно:** В Symfony 6+ старая архитектура `firewall` (с `entry_point` и `provider`) заменена более гибкой системой `authenticators`. Это делает код чище и модульнее.

---

### 2. Авторизация: Что вам разрешено?

Здесь вступают в силу три инструмента: **Роли**, **Voters** и **Атрибуты**.

#### A. Роли (Roles)
Роли — это простейший уровень доступа. Это набор строк, привязанных к пользователю.
*   `ROLE_USER` (стандартная для обычных пользователей).
*   `ROLE_ADMIN`.
*   `ROLE_SUPER_ADMIN`.

**Где хранятся?**
В методе `getRoles()` вашего класса `User` (реализация интерфейса `UserInterface`).

```php
class User extends AbstractUser
{
    private array $roles = [];

    public function getRoles(): array
    {
        // Роли из БД + стандартная ROLE_USER
        return array_merge($this->roles, ['ROLE_USER']);
    }
}
```

**Использование:**
```php
// В контроллере
#[IsGranted('ROLE_ADMIN')]
public function deleteProduct(Product $product): Response
```
Или в шаблоне:
```twig
{% if is_granted('ROLE_ADMIN') %}
    <button>Delete</button>
{% endif %}
```

**Ограничение ролей:** Они глобальны. `ROLE_ADMIN` может удалить *любой* продукт. Если нужно проверить, что админ удаляет *свой* продукт — ролей недостаточно.

---

#### B. Voters (Голосовавшие)
**Voter** — это класс, который реализует логику проверки доступа к **конкретному объекту**.
Это ответ на вопрос: *«Имеет ли этот пользователь право делать действие X с этим объектом Y?»*

**Почему они нужны?**
Потому что доступ часто зависит от контекста:
*   Владелец статьи может её редактировать.
*   Менеджер отдела может видеть заявки своего отдела, но не чужого.
*   Пользователь может просматривать заказ только после того, как он подтвержден.

#### Как устроен Voter:

```php
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProductVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        // Этот voter работает только для объекта Product
        // и атрибутов 'DELETE' и 'EDIT'
        return in_array($attribute, ['DELETE', 'EDIT']) && $subject instanceof Product;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case 'DELETE':
                // Логика: Только владелец или SUPER_ADMIN может удалить
                return $subject->getOwner()->getId() === $user->getId() 
                   || in_array('ROLE_SUPER_ADMIN', $user->getRoles());
            
            case 'EDIT':
                // Логика: Владелец может редактировать, если продукт в статусе "draft"
                return $subject->getOwner()->getId() === $user->getId() 
                   && $subject->getStatus() === 'draft';
        }

        return false;
    }
}
```

**Регистрация:**
Voter автоматически обнаруживается Symfony, если он реализует интерфейс `VoterInterface` (в новых версиях достаточно наследоваться от `Voter`) и зарегистрирован как сервис.

**Использование:**
```php
// В контроллере
#[IsGranted('DELETE', 'product')] // Атрибут 'DELETE', объект $product
public function deleteProduct(Product $product): Response
{
    // Если voter вернул false, выбросится AccessDeniedException (403)
}
```

В Twig:
```twig
{% if is_granted('DELETE', product) %}
    <a href="{{ path('product_delete', {id: product.id}) }}">Delete</a>
{% endif %}
```

---

### 3. В каких случаях что использовать?

#### Выбирайте **Роли**, когда:
1.  **Глобальный доступ:** Пользователь имеет право делать действие над *всеми* сущностями данного типа.
    *   Пример: `ROLE_EDITOR` может публиковать *любые* статьи.
2.  **Доступ к страницам/контроллерам:**
    *   `/admin/dashboard` — только `ROLE_ADMIN`.
    *   `/profile` — только аутентифицированные (`IS_AUTHENTICATED_FULLY`).
3.  **Простая иерархия:** Владелец < Менеджер < Администратор.

#### Выбирайте **Voters**, когда:
1.  **Контекстный доступ:** Права зависят от данных конкретного объекта.
    *   *Пример:* «Пользователь может удалить свой заказ, но не чужой».
    *   *Пример:* «Менеджер может видеть заявки только своего региона».
2.  **Сложная бизнес-логика:**
    *   Доступ зависит от даты, статуса, связей с другими таблицами.
3.  **Одна и та же роль, разный доступ:**
    *   Два пользователя с ролью `ROLE_CUSTOMER`: один заказал вчерашний товар (доступно), другой — сегодняшний (недоступно, пока не оплачен). Роли одинаковые, а доступ разный — нужен Voter.

---

### 4. Пример «Гибридной» логики (Best Practice)

Часто комбинация ролей и Voters дает самый чистый код.

**Задача:** Только `ROLE_ADMIN` может удалять *любые* заказы. Владелец заказа может удалить *свой* заказ, если он еще не отправлен.

```php
class OrderVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === 'DELETE' && $subject instanceof Order;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user) return false;

        // 1. Быстрая проверка: Если пользователь СЮПЕР_АДМИН — разрешаем сразу
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return true;
        }

        // 2. Если не супер-админ, проверяем владельца
        if ($subject->getOwner() !== $user) {
            return false;
        }

        // 3. Бизнес-правило: нельзя удалить отправленный заказ
        return $subject->getStatus() !== OrderStatus::SHIPPED;
    }
}
```

Здесь:
*   `ROLE_SUPER_ADMIN` обрабатывается внутри Voter (через роли).
*   Логика владельца и статуса обрабатывается логикой Voter.
*   Контроллер просто использует `#[IsGranted('DELETE', 'order')]` и не знает деталей.

---

### 5. Типичные ошибки

1.  **Использование Voters для проверки ролей:**
    ```php
    // Плохо: Voter проверяет роль
    if (in_array('ROLE_ADMIN', $user->getRoles())) { return true; }
    ```
    *Лучше:* Оставьте роли для ролей. Voters должны проверять *контекст объекта* (владение, статус, связи).

2.  **Непроверка `IS_AUTHENTICATED_FULLY`:**
    Если вы используете API с JWT, убедитесь, что ваш firewall требует полной аутентификации, иначе `$token->getUser()` может быть `null` (в случае `IS_AUTHENTICATED_ANONYMOUSLY`, хотя в Symfony 6 анонимные пользователи удалены, но логика похожа).

3.  **Сложные Voters:**
    Если Voter стал на 200 строк — вы нарушили SRP. Возможно, логику стоит вынести в отдельный сервис `AccessPolicyChecker`, который Voter будет вызывать.

---

### Итог

*   **Аутентификация** — это «Кто ты?» (Authenticator + User Provider).
*   **Роли** — это «Что тебе вообще разрешено делать?» (Глобальные права).
*   **Voters** — это «Можешь ли ты сделать это с *этим* объектом?» (Контекстные права).

**Правило:**
1.  Если доступ глобальный и простой → **Роль + Атрибут `IsGranted('ROLE_X')`**.
2.  Если доступ зависит от данных объекта → **Voter + Атрибут `IsGranted('ACTION', $object)`**.
3.  Логика Voters должна быть чистой, тестируемой и краткой.

> ⏱ 34.5s · prompt 21213 tok · output 2509 tok

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

**Кандидат:**

Это один из самых сложных аспектов интеграции DDD (Domain-Driven Design) и ORM (Object-Relational Mapping), потому что их парадигмы конфликтуют:
*   **Value Object (VO):** Имутабилен, равен по значению, не имеет ID, всегда валиден.
*   **Doctrine Entity/Mapping:** Мутация через setters, идентичность по ID, состояние может быть «грязным» (dirty) до сохранения.

Вот три основных способа маппинга VO в Doctrine, от самого распространенного к наиболее «чистому» архитектурно.

---

### Вариант 1: Custom Type (Самый популярный и практичный)

Вы создаете свой тип данных в Doctrine, который знает, как сериализовать объект VO в строку/JSON и десериализовать обратно.

#### Как реализовать:
1.  Создаем класс `Money` (VO):
    ```php
    final readonly class Money 
    {
        public function __construct(private int $amount, private string $currency) 
        {
            if ($amount < 0) throw new \InvalidArgumentException('Amount must be positive');
        }

        public function toInt(): int { return $this->amount; }
        public function getCurrency(): string { return $this->currency; }

        // Магические методы для сравнения и отображения
        public function __toString(): string {
            return "{$this->amount} {$this->currency}"; 
        }
    }
    ```

2.  Создаем `MoneyType` (наследник `Type` от Doctrine):
    ```php
    use Doctrine\DBAL\Types\Types;
    use Doctrine\DBAL\Platforms\AbstractPlatform;

    final class MoneyType extends Type
    {
        public const NAME = 'money';

        public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
        {
            // Например, храним как JSON или строку "100USD"
            return $platform->getVarcharTypeDeclarationSQL(['length' => 50]); 
        }

        public function convertToPHPValue($value, AbstractPlatform $platform)
        {
            if (null === $value) return null;

            // Парсим строку "100USD" в объект Money
            $parts = explode(' ', $value); // Упрощенный пример
            if (count($parts) !== 2) throw new \RuntimeException("Invalid Money format");
            
            return new Money((int)$parts[0], $parts[1]);
        }

        public function convertToDatabaseValue($value, AbstractPlatform $platform)
        {
            if (null === $value) return null;
            if (!$value instanceof Money) throw new \InvalidArgumentException('Expected Money');
            
            // Сериализуем в строку
            return $value->__toString(); 
        }

        public function getName(): string
        {
            return self::NAME;
        }
    }
    ```

3.  Использование в Entity:
    ```php
    #[ORM\Entity]
    class Order
    {
        #[ORM\Id]
        #[ORM\Column(type: 'integer')]
        private ?int $id = null;

        // Маппим свойство entity на наш кастомный тип
        #[ORM\Column(type: MoneyType::NAME)]
        private Money $total;

        public function setTotal(Money $money): void 
        {
            // Здесь мы меняем состояние Entity, но сам VO остается immutable
            $this->total = $money; 
        }

        public function getTotal(): Money 
        {
            return $this->total;
        }
    }
    ```

#### Плюсы:
*   **Чистота домена:** В коде вы всегда работаете с объектом `Money`, а не с `$amount` и `$currency` отдельно.
*   **Валидация при создании:** Конструктор `Money` гарантирует, что объект валиден.
*   **Простота:** Не нужно менять структуру таблицы (если храните в одном поле).

#### Минусы и подводные камни:
*   **Скрытие логики:** Логики парсинга/сериализации спрятана в `Type`. Если формат меняется, нужно менять тип и мигрировать данные.
*   **Инкапсуляция нарушена?** Нет, инкапсуляция VO сохранена. Но Entity знает о `MoneyType`.
*   **Поиск:** Вы не можете легко делать SQL-запросы по компонентам VO (например, `WHERE amount > 100`), если данные хранятся в одной строке. Придется парсить в SQL или использовать JSON-функции Postgres.

---

### Вариант 2: Маппинг в отдельные поля (Embedded Object)

Если VO имеет несколько свойств (например, `Address` с `street`, `city`, `zip`), вы маппите каждый компонент в отдельное поле таблицы.

#### Как реализовать:
В Doctrine 2.x/3.x нет встроенного `EmbeddedObject` в том виде, как в JPA, но это можно сделать через **Composite Primary Key** или просто **обычные поля**.

Лучший подход — использовать **Embedded Entity** (через пакет `jms/serializer` или просто вручную). Но в чистом Doctrine чаще делают так:

```php
final readonly class Address
{
    public function __construct(
        private string $street,
        private string $city,
        private string $zip
    ) {}
    // Getters...
}

#[ORM\Entity]
class User
{
    #[ORM\Id]
    private int $id;

    // Отдельные поля, соответствующие компонентам VO
    #[ORM\Column]
    private string $addressStreet;

    #[ORM\Column]
    private string $addressCity;

    #[ORM\Column]
    private string $addressZip;

    // Приватное поле для кэширования VO
    private ?Address $address = null;

    // Геттер возвращает VO
    public function getAddress(): Address
    {
        if (null === $this->address) {
            // Логика восстановления из отдельных полей
            $this->address = new Address($this->addressStreet, $this->addressCity, $this->addressZip);
        }
        return $this->address;
    }

    // Сеттер принимает VO и обновляет поля Entity
    public function setAddress(Address $address): void
    {
        $this->addressStreet = $address->getStreet();
        $this->addressCity = $address->getCity();
        $this->addressZip = $address->getZip();
        
        // Важный момент: сбрасываем кэш, чтобы следующий вызов getAddress() передал новый объект
        // Но т.к. VO immutable, старый объект уже не изменится. 
        // Однако, если мы хотим, чтобы Doctrine видел изменение полей, они должны быть public или у нас есть magic method __set (лучше избегать).
        // В современном подходе лучше всего:
        // Просто храним поля, а VO создаем на лету.
    }
}
```

*Примечание: Этот подход требует много ручной работы, чтобы синхронизировать поля Entity и объект VO.*

#### Плюсы:
*   **Производительность запросов:** Можно делать `WHERE city = 'Paris'` напрямую в SQL.
*   **Индексы:** Можно поставить индекс на `zip` или `city`.

#### Минусы:
*   **Шаблонный код:** Для каждого VO нужно писать геттеры/сеттеры для каждого поля.
*   **Нарушение SRP:** Entity знает структуру внутреннего VO.
*   **Сложность:** Легко забыть обновить одно поле при смене другого.

---

### Вариант 3: JSON Column (Самый современный и гибкий для Postgres)

Если у вас PostgreSQL, используйте тип `jsonb`. Храните весь VO как JSON-объект.

#### Как реализовать:
1.  Поле в Entity: `#[ORM\Column(type: Types::JSON)]`
2.  Тип данных в PHP: `array` или `object`.
3.  Но мы хотим работать с VO!

Здесь снова спасает **Custom Type** или **Converter**. Но проще всего использовать вариант 1 (Custom Type), который внутри сериализует в JSON.

```php
// В MoneyType::convertToDatabaseValue
return json_encode([
    'amount' => $value->getInt(),
    'currency' => $value->getCurrency()
]);

// В MoneyType::convertToPHPValue
$data = json_decode($value, true);
return new Money($data['amount'], $data['currency']);
```

#### Плюсы:
*   **Гибкость:** Можно добавить новое поле в VO (например, `exchange_rate`) без миграции таблицы.
*   **Инкапсуляция:** Таблица хранит «черный ящик», который понимает только домен.
*   **Поиск:** В Postgres можно индексировать JSONB-поля и делать частичные индексы.

#### Минусы:
*   **Медленнее:** JSON-операции в SQL сложнее, чем простые сравнения строк/чисел.
*   **Менее читаемая схема:** В DBeaver/Navicat вы видите blob JSON, а не отдельные колонки.

---

### Ключевые подводные камни (Pitfalls)

1.  **Мутация через Doctrine:**
    Doctrine часто работает с «сырыми» данными. Если вы используете магические методы `__get`/`__set` в Entity, чтобы «обмануть» Doctrine, вы можете потерять инкапсуляцию.
    *   *Решение:* Старайтесь держать свойства Entity `private`, а доступ давать только через методы. Но если VO хранится в отдельном поле типа `json`, вы можете сделать его `private` и управлять только через конструкторы/фабрики.

2.  **Сравнение объектов:**
    Когда вы меняете VO в Entity, Doctrine должен понять, что данные изменились (dirty checking).
    *   Если VO — это объект, то `$entity->setTotal(new Money(100, 'USD'))` заменит ссылку. Doctrine увидит, что значение свойства `total` изменилось, и отметит entity как dirty. Это работает.
    *   *Нюанс:* Если VO является `readonly` и вы не создали новый экземпляр, а попробовали изменить его (ошибка, т.к. readonly), все ок. Если бы VO был mutable, вы бы мутировали объект внутри Entity, и Doctrine **мог бы не заметить** изменения (если он использует рефлексии для сравнения значений, а не ссылок, то заметит; но обычно он сравнивает ссылки или значения, завися от типа). Для VO это безопасно, потому что мы всегда создаем новый объект.

3.  **Наследование:**
    Если ваша Entity наследуется от другой, а VO хранится в базовом классе, убедитесь, что `Custom Type` зарегистрирован глобально, иначе наследник может не найти его.

4.  **Валидация на уровне БД:**
    VO гарантирует валидность в PHP. Но если кто-то напрямую обновит запись в БД (через консоль или другую систему), данные могут стать невалидными (например, `amount = -1`).
    *   *Решение:* Используйте triggers в БД или проверяйте данные при загрузке в `Type::convertToPHPValue` (выбрасывать исключение, если формат невалиден).

---

### Итоговая рекомендация

Для большинства случаев в современном PHP-стике (Symfony + Postgres):

1.  **Простые VO (Money, DateRange, Slug):** Используйте **Custom Type**. Храните в одном поле (`varchar` или `jsonb`). Это самый чистый код.
2.  **Сложные VO (Address, Geolocation):** Если нужно часто искать по отдельным частям (`city`), используйте **отдельные поля** или **JSONB с индексами**. Если поиск не нужен — JSONB.
3.  **Никогда не храните VO как `array` напрямую в Entity** без конвертации в объект при чтении. Всегда возвращайте полноценный PHP-объект `readonly class`.

**Главное правило:** **Домен не должен знать о Doctrine.**
*   `Order` (Entity) знает, что у него есть `Money`.
*   `Money` (VO) знает, как себя сериализовать/десериализовать (через метод `toJson()`/`fromJson()`, который использует `MoneyType`).
*   `MoneyType` знает, как сохранить это в БД.

Это сохраняет чистоту архитектуры.

> ⏱ 39.5s · prompt 23774 tok · output 2853 tok

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

**Кандидат:**

Это вопрос, на котором «спотыкаются» 80% разработчиков при переходе от CRUD к DDD. Главная проблема: **Doctrine — это ORM (Object-Relational Mapper), а не Object-Relational Aggregator.** Он не знает, что такое «агрегат». Он знает только про сущности и их связи.

Если вы неправильно определите границу транзакции и порядок `flush()`, вы получите:
1.  **Атомарность:** Часть данных сохранена, часть — нет (при падении).
2.  **Нарушение инвариантов:** Состояние БД временно невалидно (например, сумма заказа не равна сумме позиций).
3.  **Голодные блокировки:** Долгие транзакции блокируют другие запросы.

Давайте разберем на примере агрегата **«Заказ» (Order)**.

---

### 1. Структура домена (DDD)

У нас есть:
*   **Агрегатный корень:** `Order`.
*   **Дочерние сущности (Child Entities):** `OrderItem` (позиции заказа).
*   **Зависимые объекты:** `Money`, `Address` (Value Objects).

**Правило инкапсуляции:**
Никто снаружи (ни контроллер, ни сервис) **не должен** иметь доступа к коллекции `OrderItem` напрямую. Все изменения должны проходить через методы агрегатного корня `Order`.

```php
// Domain/Order.php
final class Order
{
    private int $id;
    private Money $total;
    private Status $status;
    
    /**
     * @var Collection<int, OrderItem>
     */
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->status = Status::Pending;
        $this->total = Money::zero('USD');
    }

    // Публичный API агрегата
    public function addItem(Product $product, int $quantity): void
    {
        if ($this->isClosed()) {
            throw new DomainException('Cannot add items to closed order');
        }

        $item = new OrderItem($product, $quantity);
        $this->items->add($item);
        
        // Инвариант: Пересчитываем сумму только внутри агрегата
        $this->recalculateTotal();
    }

    public function confirm(): void
    {
        if ($this->items->isEmpty()) {
            throw new DomainException('Order cannot be confirmed without items');
        }
        $this->status = Status::Confirmed;
    }

    private function recalculateTotal(): void
    {
        $sum = Money::zero('USD');
        foreach ($this->items as $item) {
            $sum = $sum->add($item->getSubtotal());
        }
        $this->total = $sum;
    }

    // Getters...
}
```

---

### 2. Маппинг в Doctrine

Здесь критически важно настроить связь `OneToMany` / `ManyToOne` и, главное, **управление каскадом и удалением**.

```php
#[ORM\Entity]
class Order
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: MoneyType::NAME)]
    private Money $total;

    #[ORM\Column(type: Types::STRING, length: 20)]
    private string $status;

    #[ORM\OneToMany(
        mappedBy: 'order',
        targetEntity: OrderItem::class,
        cascade: ['persist'], // Важный момент: только persist
        orphanRemoval: true   // Важный момент: удаление "сирот"
    )]
    private Collection $items;

    // ... конструктор и методы из примера выше ...
}

#[ORM\Entity]
class OrderItem
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity;

    // ...
}
```

**Ключевые настройки:**
1.  `cascade: ['persist']`: Когда мы сохраняем `Order`, Doctrine автоматически сохранит все `OrderItem`, добавленные в коллекцию. Мы **не** вызываем `em->persist($item)` для каждой позиции вручную.
2.  `orphanRemoval: true`: Если вы удалите элемент из коллекции `Order->items` (например, `$order->removeItem($item)`), то при сохранении `Order` Doctrine **удалит** эту запись из таблицы `order_items`. Это реализует инкапсуляцию: снаружи нельзя удалить позицию, не спросив агрегат.

---

### 3. Кто вызывает `flush()` и где граница транзакции?

**Правильный ответ: Application Service (или Use Case Handler).**

Доменный агрегат **никогда** не знает о базе данных. Он не вызывает `flush()`. Он просто меняет свое внутреннее состояние.
Репозиторий (Repository) в идеальной DDD-модели — это порт (интерфейс), а его реализация в Doctrine — адаптер. Но даже адаптер часто просто делает `save()` или `remove()`, не вызывая `flush()`, чтобы дать контроллеру/сервису возможность группировать операции.

#### Архитектура потока:

1.  **Controller** получает запрос.
2.  **Application Service** (Use Case) начинает транзакцию.
3.  Service загрузил агрегат через Repository.
4.  Service вызывает доменные методы: `$order->addItem(...); $order->confirm();`
5.  Service вызывает `$repository->save($order);` (или просто `flush()`, если репозиторий не делает магии).
6.  **Транзакция завершается успешно или откатывается.**

#### Пример кода (Application Layer):

```php
class OrderService
{
    public function __construct(
        private OrderRepository $orderRepo,
        private EntityManagerInterface $em
    ) {}

    public function createAndConfirmOrder(int $userId, array $productIds): Order
    {
        // 1. Начинаем транзакцию вручную (или используем декоратор, но для примера явно)
        $transaction = $this->em->getConnection()->beginTransaction();

        try {
            // 2. Создаем или получаем агрегат
            $order = new Order();
            $order->setUser($this->userRepo->find($userId));

            foreach ($productIds as $productId) {
                $product = $this->productRepo->find($productId);
                
                // 3. Модифицируем агрегат (доменная логика)
                // Внутри addItem() мы пересчитываем сумму и проверяем инварианты
                $order->addItem($product, 1);
            }

            // 4. Финализируем состояние
            $order->confirm();

            // 5. Сохраняем агрегат
            // Repository просто вызывает em->persist($order).
            // Благодаря cascade: ['persist'], все OrderItem тоже попадут в UnitOfWork.
            $this->orderRepo->save($order);

            // 6. Flush: Здесь происходит генерация SQL INSERT/UPDATE и commit транзакции
            $this->em->flush();

            $transaction->commit();

            return $order;

        } catch (Throwable $e) {
            // Если что-то пошло не так (даже на этапе валидации домена), откатываем всё
            if ($transaction->isActive()) {
                $transaction->rollBack();
            }
            throw $e;
        }
    }
}
```

### Почему именно так?

1.  **Атомарность:** Все изменения (`Order`, `OrderItem`) сохраняются в один SQL-транзакционный блок. Если `flush()` упадет на 5-й из 10 позиций — откатится вся операция. Никаких «половинчатых» заказов.
2.  **Инкапсуляция инвариантов:** Метод `addItem()` внутри `Order` гарантирует, что сумма пересчитывается. Если бы мы сохраняли `OrderItem` до того, как `Order` пересчитал сумму, в БД могло бы временно лежать несогласованное состояние (хотя внутри транзакции другие сессии это не увидят до commit).
3.  **Управление Unit of Work (UoW):**
    *   `em->persist($order)` добавляет корень в UoW.
    *   `cascade` заставляет UoW добавить все `OrderItem`.
    *   `em->flush()` генерирует SQL для всех объектов в UoW.
    *   Order of operations в SQL будет правильным: сначала `INSERT INTO orders`, потом `INSERT INTO order_items` (так как `order_items` ссылается на `orders.id`).

### Подводные камни и антипаттерны

#### ❌ Антипаттерн 1: Flush внутри Repository
```php
class OrderRepository
{
    public function save(Order $order): void {
        $this->em->persist($order);
        $this->em->flush(); // ПЛОХО!
    }
}
```
**Почему плохо?** Если в сервисе вы делаете:
```php
$order->addItem($item1);
$repo->save($order); // Flush #1
$order->addItem($item2);
$repo->save($order); // Flush #2
```
Вы совершаете две отдельные транзакции. Если вторая упадет, первая уже закоммичена. Плюс, вы делаете лишние запросы к БД. **Решение:** Repository должен только `persist()`, а `flush()` вызывается на уровне сервиса или контроллера (или в декораторе `Transactional`).

#### ❌ Антипаттерн 2: Доступ к коллекции извне
```php
// В Controller или Service:
$items = $order->getItems();
$items->add(new OrderItem($product, 1)); // ПЛОХО!
$order->recalculateTotal(); // Нужно вспомнить, что это нужно сделать
```
**Почему плохо?** Вы нарушили инкапсуляцию. Вы можете забыть вызвать `recalculateTotal()`.
**Решение:** В `Order` сделать метод `addItem(Product, Qty)`, который делает и `add`, и `recalculate`. Коллекция `items` должна быть приватной, а геттер возвращает **Immutable** коллекцию (или просто запрещает мутацию снаружи).

#### ❌ Антипаттерн 3: Загрузка агрегата вместе с большим количеством дочерних сущностей
Если у заказа 1000 позиций, и вам нужен только статус заказа, не делайте `JOIN` со всеми позициями.
**Решение:**
*   Используйте `lazy` loading для коллекции `items`.
*   Или создайте отдельный DTO/Query-класс для чтения (CQRS-подход), если чтение отличается от записи.

### Итоговая схема работы

1.  **Domain:** `Order` и `OrderItem` знают друг о друге через связь. `Order` управляет жизненным циклом `OrderItem`.
2.  **Persistence (Doctrine):**
    *   `Order` — Entity.
    *   `OrderItem` — Entity.
    *   Связь `OneToMany` с `cascade: ['persist']` и `orphanRemoval: true`.
3.  **Application:**
    *   Загружаем агрегат.
    *   Меняем состояние через методы `Order`.
    *   `em->persist($order)`.
    *   `em->flush()` (или `commit()` транзакции).
4.  **Result:** В БД атомарно появляются/изменяются запись в `orders` и все связанные записи в `order_items`.

Такой подход гарантирует, что **граница агрегата совпадает с границей транзакции**. Это основа надежного DDD-дизайна в PHP.

> ⏱ 39.0s · prompt 26690 tok · output 2785 tok

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

**Кандидат:**

Проблема N+1 — это классика работы с ORM. По сути, вы получаете 1 запрос на получение списка сущностей и N запросов на ленивую загрузку дочерних сущностей для каждой из этих записей.

Ниже описан алгоритм диагностики и детальный разбор способов решения с их компромиссами (trade-offs).

---

### 1. Диагностика: Как поймать N+1?

Нельзя догадываться. Нужно видеть реальные SQL-запросы.

#### Инструменты:

1.  **Symfony Profiler (Самый удобный):**
    *   Включите Profiler.
    *   Откройте тормозящую страницу.
    *   В разделе **DB** (Database) посмотрите список запросов.
    *   Если вы видите повторяющийся паттерн:
        1.  `SELECT * FROM orders`
        2.  `SELECT * FROM order_items WHERE order_id = 1`
        3.  `SELECT * FROM order_items WHERE order_id = 2`
        4.  `SELECT * FROM order_items WHERE order_id = 3`
    *   **Вердикт:** Это N+1.

2.  **Дамп через Logger (Для CLI/Console команд):**
    Если Profiler недоступен, настройте логгер Doctrine, чтобы он выводил каждый SQL-запрос с таймингом.
    ```php
    // В services.yaml или через DI
    doctrine:
        dbal:
            logging:
                enabled: '%kernel.debug%'
                // Подключите свой логгер, который пишет в файл
    ```

3.  **Расширение для IDE (Xdebug + PhpStorm/IntelliJ):**
    В PhpStorm есть панель «SQL» или «Database Queries». При запуске приложения с включенным Xdebug она покажет все запросы в реальном времени. Если видите «пачку» похожих запросов — это N+1.

4.  **Библиотека `dusterio/doctrine-orm-debug`:**
    Специализированный инструмент, который подсвечивает потенциальные N+1 запросы прямо в профайлере Symfony более наглядно, чем стандартный.

---

### 2. Способы решения и Trade-offs

#### Способ 1: Eager Loading (Жадная загрузка)

Самое очевидное решение: загрузить все нужные данные в одном запросе.

**Как сделать:**
Вместо `find()` или репозитория с `findAll()` используйте DQL/QueryBuilder с `JOIN`.

```php
// Плохо (N+1):
$orders = $repo->findAll(); 
foreach ($orders as $order) {
    echo $order->getItems()->first(); // Лайза здесь!
}

// Хорошо (Eager Join):
$qb = $em->createQueryBuilder();
$qb->select('o', 'i') // Выбираем оба сущности
   ->from(Order::class, 'o')
   ->leftJoin(OrderItem::class, 'i', 'WITH i.order = o'); // Присоединяем
$orders = $qb->getQuery()->getResult(); 
```

**Trade-offs:**
*   ✅ **Плюс:** Один запрос к БД. Скорость максимальна.
*   ❌ **Минус:** **Память.** Если у вас 1000 заказов, а у каждого по 100 позиций, вы загружаете 100,000 объектов в PHP-память сразу. Если страница отображает только первые 20 заказов — вы потратили ресурсы на 980 лишних.
*   ❌ **Минус:** **Сложность DQL.** Если иерархия глубокая (Order -> Item -> Product -> Category), DQL становится сложным и трудночитаемым.

---

#### Способ 2: Explicit Lazy Loading (Явное ленивое)

По умолчанию Doctrine использует Lazy Loading для коллекций `OneToM`. Но если вы итерируете по коллекции, триггерится запрос.

**Решение:** Не трогать коллекцию, если она не нужна.

**Trade-offs:**
*   ✅ **Плюс:** Минимальная нагрузка на БД и память.
*   ❌ **Минус:** Требует дисциплины. Вы должны точно знать, какие поля нужны для текущей операции (Read Model). Часто это требует создания отдельных DTO или Query-классов (CQRS подход), а не использования Entity напрямую.

---

#### Способ 3: Partial Objects / SELECT конкретных полей

Загружать только те поля, которые нужны.

**Как сделать:**
В DQL указать конкретные поля, а не `*`.
```php
$qb->select('o.id', 'o.status', 'i.id', 'i.quantity');
```

**Trade-offs:**
*   ✅ **Плюс:** Меньше трафика сети между БД и PHP. Меньше памяти.
*   ❌ **Минус:** Возвращаются массивы или «частичные» объекты. Вы теряете доступ к другим методам сущности (если они не кэшированы). Нельзя использовать это в сложных сценариях, где нужна полная инкапсуляция Entity.

---

#### Способ 4: Fetch Mode `EAGER` в маппинге (С осторожностью!)

В аннотациях/атрибутах можно указать:
```php
#[ORM\OneToMany(mappedBy: 'order', fetch: 'EAGER')]
```

**Trade-offs:**
*   ✅ **Плюс:** Не нужно писать DQL с JOIN. Просто вызываете `find()`, и данные уже есть.
*   ❌ **Минус:** **Глобальность.** Это влияет на ВСЕ запросы, где встречается эта сущность. Даже если вам не нужны дети, они будут загружены.
*   ❌ **Минус:** **Сложность управления.** Если у вас есть разные сценарии (лист с кратким описанием заказов и детальная страница заказа), `EAGER` не позволит разгрузить первый сценарий.

> **Совет:** Избегайте глобального `EAGER` в маппинге. Лучше управлять загрузкой явно через DQL в репозиториях.

---

#### Способ 5: Денормализация / Read Model (CQRS)

Если список заказов отображается часто, а обновление редко — создайте отдельную таблицу `order_views` или используйте материализованное представление (Materialized View) в Postgres.

**Как сделать:**
1.  Таблица `orders` (Write Model) — источник правды.
2.  Таблица `order_summaries` (Read Model) — содержит предвычисленные поля (сумма, количество позиций, имя первого товара).
3.  При изменении заказа (через доменные события или триггеры) обновляем `order_summaries`.
4.  Страница списка читает только из `order_summaries`.

**Trade-offs:**
*   ✅ **Плюс:** Максимальная производительность чтения. Простой SQL.
*   ❌ **Минус:** **Сложность архитектуры.** Нужно следить за согласованностью данных (Eventual Consistency).
*   ❌ **Минус:** Избыточная запись (Write Amplification) — при каждом изменении заказа нужно обновлять и вторую таблицу.

---

#### Способ 6: Кэширование результата запроса

Если данные меняются редко, кэшируйте результат всего запроса (или части) в Redis/Memcached.

**Trade-offs:**
*   ✅ **Плюс:** Мгновенный ответ, если кэш «попал».
*   ❌ **Минус:** **Устаревание данных.** Пользователь может видеть старый список. Нужно реализовать логику инвалидации кэша при каждом `flush()` или изменении сущности.
*   ❌ **Минус:** Кэш не решит проблему памяти внутри PHP-процесса, если кэш промахнется.

---

### Итоговая матрица выбора

| Ситуация | Рекомендуемое решение | Почему |
| :--- | :--- | :--- |
| **Лист сущностей (Dashboard/List)** | **Eager Join (DQL)** + **Limit/Pagination** | Загружаем только ту страницу, которую показываем. Один запрос. |
| **Детальная страница одной сущности** | **Lazy Loading** (по умолчанию) | Мы знаем, что нужны все данные для одного объекта. N+1 здесь не страшен (N=1). |
| **Сложные агрегаты с вложенными коллекциями** | **Partial Select** или **Read Model** | Не загружаем лишние вложенные сущности, если они не нужны для отображения. |
| **Высоконагруженные отчеты** | **Materialized Views** или **Специализированные SQL-запросы** | БД оптимизирует такие запросы лучше, чем ORM. |

### Чек-лист действий при обнаружении N+1:

1.  **Определите, нужны ли вам эти данные?**
    *   Нет? Уберите обращение к коллекции из кода страницы.
    *   Да? Продолжайте.
2.  **Сколько записей?**
    *   Меньше 50? `Eager Join` — лучший выбор.
    *   Больше 50? `Eager Join` может убить память. Рассмотрите `Partial Select` или разбивку на отдельные запросы с кэшированием.
3.  **Как часто меняются данные?**
    *   Часто? Избегайте кэшей, используйте свежие данные из БД через `Eager Join`.
    *   Редко? Кэшируйте результат запроса.

**Главное правило:** Не боритесь с N+1 «в лоб», если это не нужно. Иsometimes, один сложный `JOIN` хуже, чем несколько простых запросов, потому что он усложняет код и затрудняет поддержку. Выбирайте баланс между **производительностью** и **поддерживаемостью**.

> ⏱ 32.4s · prompt 29530 tok · output 2271 tok

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

**Кандидат:**

Это один из самых частых спорных вопросов в архитектуре: «Если код уже проверяет, зачем дублировать проверки в БД?»

Короткий ответ: **Код приложения — это первый барьер (UX и бизнес-логика), а ограничения БД — это последний барьер (Data Integrity).**

База данных — это **источник правды** (Source of Truth). Код приложения может упасть, быть обходным (через консоль, через другую микросервисную систему, через прямой SQL-запрос разработчика в 3 часа ночи). Ограничения в БД гарантируют, что **невалидные данные никогда не попадут в хранилище**, независимо от того, кто и как их туда отправил.

Давайте разберем, какие именно ограничения использовать и почему они важны.

---

### 1. Почему «код проверяет» — недостаточно?

1.  **Конкурентность (Race Conditions):**
    Два пользователя одновременно снимают последнюю единицу товара со склада.
    *   *В коде:* `if (stock > 0) { stock-- }` — оба читают `1`, оба пишут `0`. Итог: `-1` или потерянная запись.
    *   *В БД:* `UPDATE products SET stock = stock - 1 WHERE id = ? AND stock > 0` — атомарная операция. Или `CHECK (stock >= 0)`.
2.  **Обходные пути:**
    *   Администратор выполняет прямой SQL-запрос в DBeaver.
    *   Другой сервис (на Java, например) пишет в ту же таблицу.
    *   Скрипт миграции или бэкап/восстановление.
    *   В этих случаях ваш PHP-код не участвует.
3.  **Тесты:**
    Юнит-тесты изолируют объект. Интеграционные тесты проверяют связку. Но если в коде есть баг в логике валидации, тесты могут его пропустить, если они не покрывают этот edge-case. Ограничение в БД поймает ошибку на этапе `INSERT`.

---

### 2. Какие средства PostgreSQL использовать?

Postgres предоставляет мощный набор инструментов для обеспечения целостности.

#### A. `CHECK` Constraints (Локальные правила)
Простейшие правила на уровне строки.

```sql
-- Запрет отрицательных сумм
ALTER TABLE orders ADD CONSTRAINT chk_order_total_positive CHECK (total_amount > 0);

-- Запрет будущего времени создания (если время генерируется на стороне БД)
ALTER TABLE events ADD CONSTRAINT chk_created_at_not_future CHECK (created_at <= NOW());
```
*   **Когда использовать:** Всегда. Это дешевые проверки, которые выполняются до записи.

#### B. `NOT NULL` и `DEFAULT`
Базовая гигиена.

```sql
email VARCHAR(255) NOT NULL,
status VARCHAR(20) NOT NULL DEFAULT 'pending'
```
*   **Почему:** Если поле обязательно по бизнес-смыслу, пусть БД упадет с `NOT NULL violation`, а не вернет `null`, который потом упадет в PHP с `TypeError`.

#### C. `UNIQUE` Constraints (Уникальность)
Критически важно для бизнес-идентификаторов.

```sql
-- В таблице users
UNIQUE (email)

-- В таблице sessions
UNIQUE (session_id)
```
*   **Когда использовать:** Для любых полей, которые должны быть уникальными (email, username, invoice_number, api_token).
*   **Почему:** Даже если у вас идеальная логика в коде, при дубле запросов (double-submit) может создаться два записки с одним email. БД скажет: `unique_violation`, и вы откатите транзакцию.

#### D. `FOREIGN KEY` (Референтальная целостность)
Самое важное ограничение.

```sql
-- В таблице order_items
order_id INTEGER REFERENCES orders(id) ON DELETE CASCADE
```
*   **Когда использовать:** Всегда, когда есть связь между таблицами.
*   **Почему:** Без FK вы можете получить «сиротские» строки (order_item без заказа) или «битые» ссылки.
*   **`ON DELETE CASCADE`:** Автоматически удаляет позиции заказа при удалении заказа. Это заменяет ручной цикл `foreach` в коде и защищает от ошибок.

#### E. Custom Triggers and Functions (Сложная бизнес-логика)
Используются, когда правила слишком сложны для `CHECK`.

```sql
-- Пример: Нельзя удалить пользователя, если у него есть незаверщенные заказы
CREATE OR REPLACE FUNCTION prevent_user_delete()
RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (
        SELECT 1 FROM orders 
        WHERE user_id = OLD.id 
        AND status != 'completed'
    ) THEN
        RAISE EXCEPTION 'User has active orders and cannot be deleted';
    END IF;
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_prevent_user_delete
BEFORE DELETE ON users
FOR EACH ROW EXECUTE FUNCTION prevent_user_delete();
```
*   **Когда использовать:** Для инвариантов, которые затрагивают несколько таблиц или сложные условия.
*   **Минус:** Сложность отладки. Ошибка будет в логах БД, а не в логах приложения. Используйте с умом.

#### F. Materialized Views / Indexes
Хотя это не «ограничения» в прямом смысле, они поддерживают целостность производительности.
*   Например, если у вас есть правило «один товар может быть в корзине один раз», вы можете создать уникальный индекс на `(user_id, product_id)` в таблице `cart_items`. Это предотвратит дубли и ускорит поиск.

---

### 3. Trade-offs: Код vs База

| Аспект | Валидация в Коде (PHP) | Ограничения в БД (Postgres) |
| :--- | :--- | :--- |
| **Понятность** | Высокая (читается как код) | Низкая (нужен SQL, ошибки обфусцированы) |
| **Производительность** | Высокая (проверка до запроса) | Средняя (проверка при записи, но очень быстрая для простых CHECK) |
| **Гибкость** | Высокая (можно менять логику без миграции) | Низкая (нужна миграция БД, чтобы изменить правило) |
| **Надежность** | Средняя (зависит от реализации) | Высокая (гарантия на уровне ядра СУБД) |
| **Отладка** | Легко (stack trace в PHP) | Сложно (ошибки в логах БД или обфусцированные сообщения ORM) |

---

### 4. Стратегия: Где что держать?

Мой подход — **«Линии обороны»**:

1.  **Frontend / API Validation (Слой 1):**
    *   Быстрая проверка типов, форматов, наличия полей.
    *   *Цель:* Дать быстрый фидбек пользователю.
    *   *Пример:* `email` содержит `@`, `age` — число.

2.  **Domain Validation (Слой 2):**
    *   Бизнес-правила.
    *   *Цель:* Обеспечить смысловую корректность доменного объекта.
    *   *Пример:* `Money` должен быть положительным, `Order` не может быть подтвержден без позиций.
    *   *Исполнитель:* Доменные классы (Value Objects, Entities).

3.  **Database Constraints (Слой 3 — Последний шанс):**
    *   Железобетонная целостность.
    *   *Цель:* Поймать то, что проскочило через первые два слоя (баги, конкурентность, внешние источники).
    *   *Пример:* `NOT NULL`, `UNIQUE`, `FOREIGN KEY`, `CHECK (amount > 0)`.

**Важный нюанс:**
Не дублируйте сложную бизнес-логику (типа «скидка зависит от сезона и региона») в триггерах БД. Это делает БД «черным ящиком» и усложняет поддержку.
*   **В коде:** Логика.
*   **В БД:** Факты (структура, типы, связи, простые границы значений).

### Итог

Ограничения в PostgreSQL — это **инсультная страховка** для вашей базы данных. Они не заменяют код, но спасают вас от самых страшных сценариев: гонки процессов, ошибок разработки, ручных правок и внешних систем.

**Чек-лист для новой таблицы:**
1.  Есть ли `PRIMARY KEY`?
2.  Есть ли `NOT NULL` для обязательных полей?
3.  Есть ли `UNIQUE` для уникальных идентификаторов (email, token)?
4.  Есть ли `FOREIGN KEY` для всех связей с другими таблицами?
5.  Есть ли `CHECK` для значений, которые физически не могут быть вне диапазона (суммы, количества, даты)?

Если эти пункты соблюдены, ваша база данных будет «чиста» даже если ваш PHP-код сойдет с ума.

> ⏱ 30.0s · prompt 31842 tok · output 2100 tok

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

**Кандидат:**

Диагностика медленных запросов в PostgreSQL — это не магия, а инженерный процесс, который можно разбить на четкие этапы. Я использую принцип «от общего к частному»: сначала понять, *где* теряется время, а уже потом *почему* так происходит.

Вот мой пошаговый алгоритм диагностики и оптимизации.

---

### Этап 1: Сбор данных (Что именно медленно?)

Прежде чем оптимизировать, нужно точно знать, какой запрос виноват и сколько времени он занимает.

#### 1.1. Включение логирования медленных запросов
Если вы еще не знаете, какие запросы медленные, включите логирование в `postgresql.conf` или через `ALTER DATABASE`:

```sql
-- Логировать все запросы, длительностью более 500мс
log_min_duration_statement = '500ms'

-- Включить логирование плана выполнения (полезно для отладки)
log_statement = 'mod' -- или 'all' для полной детализации (осторожно, много логов!)
```
*Совет:* Лучше настроить логирование только для конкретного приложения или схемы, чтобы не заспамить лог всей БД.

#### 1.2. `pg_stat_statements` (Главный инструмент)
Это расширение, которое хранит статистику по всем выполненным запросам (среднее время, количество вызовов, строки прочитано/написано).

```sql
CREATE EXTENSION pg_stat_statements;

-- Топ-10 самых медленных запросов (по общему времени)
SELECT 
    query,
    calls,
    round(mean_exec_time::numeric, 2) AS avg_ms,
    round(total_exec_time::numeric, 2) AS total_ms,
    rows
FROM pg_stat_statements
ORDER BY mean_exec_time DESC
LIMIT 10;
```
*Что смотрим:*
*   `mean_exec_time`: Среднее время выполнения.
*   `calls`: Сколько раз выполнялся.
*   `rows`: Сколько строк вернул.
*   *Инсайт:* Если `mean_exec_time` большой, но `calls` мало — это «тяжелый» запрос. Если `calls` тысячи, а `mean_exec_time` мал — это «частый» запрос, который суммарно ест много CPU.

---

### Этап 2: Анализ плана выполнения (EXPLAIN ANALYZE)

Это самый важный шаг. Мы видим, как Postgres думает, как он выполняет запрос и где тратит время.

#### Инструмент: `EXPLAIN (ANALYZE, BUFFERS, FORMAT JSON)`

```sql
EXPLAIN (ANALYZE, BUFFERS) 
SELECT * FROM orders 
WHERE user_id = 123 
AND status = 'pending'
ORDER BY created_at DESC 
LIMIT 10;
```

#### Как читать план (ищем «узкие горлышки»):

1.  **`Seq Scan` vs `Index Scan`:**
    *   `Index Scan` / `Index Only Scan`: Хороший знак. Postgres использовал индекс.
    *   `Seq Scan` (Sequentional Scan): **Плохой знак**, если таблица большая. Это значит, что Postgres читает всю таблицу с начала до конца.
        *   *Нюанс:* Для маленьких таблиц (меньше 1000–5000 строк) `Seq Scan` может быть быстрее `Index Scan`, так как меньше I/O. Но для больших таблиц — почти всегда проблема.

2.  **`Nested Loop` vs `Hash Join` vs `Merge Join`:**
    *   Эти методы соединения строк могут быть медленными, если выборка одного из операторов огромна, а другого — мала (или наоборот).
    *   Смотрите на `actual rows`: если ожидалось 10 строк, а фактически пришло 100,000 — планы неверны.

3.  **`Rows Removed by Filter`:**
    *   Если вы видите `Filter: (status = 'pending')` и `Rows Removed by Filter` большое — это значит, что Postgres загрузил много строк из индекса/таблицы, а затем отфильтровал лишние. Это признак того, что **индекс не оптимален** (не покрывает условия фильтрации).

4.  **`Buffers` (Буферы):**
    *   `Hit Buffers`: Прочитано из кэша Postgres (быстро).
    *   `Read Buffers`: Прочитано с диска (медленно).
    *   Если `Read Buffers` доминирует — проблема в **I/O**. Нужно либо кэшировать данные, либо оптимизировать запрос так, чтобы читать меньше данных.

5.  **`Parallel Seq Scan`:**
    *   Если Postgres использует параллельный сканирование, это говорит о том, что он пытается ускорить чтение больших объемов данных. Это нормально для отчетов, но странно для OLTP-запросов (быстрых транзакций).

---

### Этап 3: Типовые причины медленных запросов и решения

#### 1. Отсутствие или неправильный индекс
*   **Признак:** `Seq Scan` на большой таблице в `WHERE` клаузе.
*   **Решение:**
    *   Создайте индекс по полям из `WHERE`.
    *   Если есть `ORDER BY` и `LIMIT`, создайте **компонованный индекс** (Composite Index): `(user_id, status, created_at)`. Порядок важен! Поля из `WHERE` (с точным совпадением) идут первыми, затем поля из `ORDER BY`.

```sql
-- Пример идеального индекса для запроса выше
CREATE INDEX idx_orders_user_status_created 
ON orders (user_id, status, created_at DESC);
```
    *   Используйте `INCLUDE` для покрытия (Covering Index), если выборка маленьких полей:
    ```sql
    CREATE INDEX idx_covering ON orders (user_id, status) INCLUDE (total_amount);
    -- Теперь Postgres не будет читать саму таблицу orders, если нужны только эти поля
    ```

#### 2. Функции в условиях `WHERE`
*   **Признак:** Индекс есть, но `Seq Scan`.
*   **Причина:** Вы используете функцию над колонкой, которую индексируете.
    ```sql
    -- Плохо: Индекс на created_at не поможет
    SELECT * FROM events WHERE DATE(created_at) = '2023-10-01';
    
    -- Хорошо: Диапазон дат
    SELECT * FROM events 
    WHERE created_at >= '2023-10-01 00:00:00' 
    AND created_at < '2023-10-02 00:00:00';
    ```
*   **Решение:** Писать условия диапазона. Если нельзя — создавать **Functional Index**:
    ```sql
    CREATE INDEX idx_event_date ON events (DATE(created_at));
    ```

#### 3. Неправильный выбор оператора соединения (Joins)
*   **Признак:** Медленный `Hash Join` или `Nested Loop`.
*   **Причина:**
    *   `Nested Loop` хорош, когда одна таблица маленькая (фильтр), а другая большая.
    *   `Hash Join` хорош, когда обе таблицы большие.
    *   Если Postgres выбирает `Nested Loop`, а таблица «внешняя» (которую он итерирует) огромная — это медленно.
*   **Решение:**
    *   Убедитесь, что фильтры (`WHERE`) сужают выборку *до* соединения.
    *   Проверьте статистики: `ANALYZE table_name;` (Postgres обновляет планы на основе статистик. Если данные изменились, статистики могут быть устаревшими).

#### 4. Выборка «всего» (`SELECT *`)
*   **Признак:** Высокие `Read Buffers`.
*   **Причина:** Вы читаете столбцы, которые не нужны, особенно если там есть `TEXT`, `JSONB`, `BLOB`. Это увеличивает размер строк и нагрузку на память/сеть.
*   **Решение:** Выбирайте только нужные столбцы. `SELECT id, name, status FROM ...`

#### 5. Большие транзакции и блокировки
*   **Признак:** Запрос зависает, `Wait Event: Lock`.
*   **Причина:** Другой процесс блокирует строки/таблицы.
*   **Диагностика:**
    ```sql
    -- Посмотреть активные блокировки
    SELECT * FROM pg_locks WHERE NOT granted;
    
    -- Посмотреть блокирующие сессии
    SELECT 
        bl.blocked_pid,
        bl.blocking_pid,
        a.usename,
        a.query
    FROM pg_locks bl
    JOIN pg_stat_activity a ON bl.pid = a.pid
    WHERE NOT bl.granted;
    ```
*   **Решение:**
    *   Сокращать длительность транзакций.
    *   Использовать `SELECT ... FOR UPDATE` только когда необходимо.
    *   Проверять, нет ли «зависших» транзакций (например, клиент обрвался, а соединение осталось открытым).

#### 6. Проблема с `JSONB`
*   **Признак:** Медленный поиск внутри JSON.
*   **Причина:** Линейный поиск по JSON-структурам.
*   **Решение:**
    *   Создать **GIN-индекс**: `CREATE INDEX idx_config ON config USING GIN (data jsonb_path_ops);`
    *   Использовать оператор `@>` (contains) или `?` (has key).
    *   Выделять часто используемые поля в отдельные колонки (денормализация).

---

### Этап 4: Продвинутые техники

1.  **Материализованные представления (Materialized Views):**
    *   Если запрос — это сложный отчет (Join'ы 5+ таблиц, агрегации), вычислите его результат один раз, сохраните в отдельную таблицу (Materialized View) и обновляйте его по расписанию или триггером.
    *   *Trade-off:* Данные не всегда актуальны (Eventual Consistency).

2.  **Разделение на CTEs (Common Table Expressions):**
    *   Иногда Postgres оптимизирует CTEs хуже, чем подзапросы, или наоборот. Попробуйте переписать запрос, используя CTEs, чтобы «зафиксировать» промежуточные результаты и упростить работу оптимизатора.

3.  **Настройка `work_mem` и `shared_buffers`:**
    *   Если запросы сортируют/группируют большие массивы данных, они могут проливаться на диск (Temp Files).
    *   Увеличьте `work_mem` (память для сортировки/hashing) для сессии с медленным запросом.
    *   Убедитесь, что `shared_buffers` (кэш данных) адекватен размеру RAM (обычно 25% RAM).

---

### Резюме: Чек-лист оптимизации

1.  **Измерьте:** `pg_stat_statements` + `EXPLAIN ANALYZE`.
2.  **Ищите Seq Scans:** Добавьте индексы.
3.  **Проверьте индексы:** Они должны покрывать `WHERE`, `JOIN`, `ORDER BY`.
4.  **Уберите `SELECT *`:** Выбирайте только нужное.
5.  **Обновите статистику:** `ANALYZE`.
6.  **Проверьте блокировки:** Кто держит ресурсы?
7.  **Разделите чтение/запись:** Если нужно, используйте Read Replicas.
8.  **Денормализуйте:** Если запрос слишком сложный, а данные меняются редко, вынесите результат в отдельную колонку/таблицу.

Помните: **Нет универсального рецепта.** Оптимизация — это итеративный процесс. Измените один параметр, замерьте результат, и только потом переходите к следующему шагу.

> ⏱ 39.7s · prompt 33989 tok · output 2784 tok

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

**Кандидат:**

**Агрегат (Aggregate)** в DDD — это кластер сущностей и объектов-значений (Value Objects), который рассматривается внешним миром как **единственная атомарная единица изменения**.

Простыми словами: это «коробка», внутри которой данные связаны жесткими инвариантами (правилами, которые всегда должны выполняться). Если кто-то снаружи меняет состояние внутри коробки, он должен сделать это через **Агрегатный Корень (Aggregate Root)**, который гарантирует, что все правила не будут нарушены.

### Как определять границы агрегата?

Главный критерий: **Атомарность транзакции**.
Вопрос: *«Какие данные должны измениться одновременно, атомарно, чтобы система осталась в валидном состоянии?»*

Если данные можно изменить по отдельности, не нарушая бизнес-логики, — они, скорее всего, должны быть в разных агрегатах.

**Правила определения границ:**

1.  **Идентификация:** Агрегат должен иметь уникальный идентификатор (ID) у своего Корня.
2.  **Инкапсуляция:** Внешний мир ссылается только на Корень. Никогда не на внутренние сущности напрямую.
3.  **Размер:** Агрегаты должны быть небольшими. Чем больше агрегат, тем сложнее保证其 (гарантировать) его целостность и тем больше объектов загружается в память (проблема N+1).
4.  **Связи:** Между агрегатами — только ссылки по ID (Value Object типа `OrderId`), а не объекты.

---

### Пример: Система бронирования билетов

Представим, что мы делаем систему продажи билетов.

**Сущности:**
*   `Event` (Событие/Концерт)
*   `Ticket` (Билет)
*   `User` (Пользователь)

#### Правильные границы:

**Агрегат 1: `Event` (Событие)**
*   **Корень:** `Event`
*   **Состав:** `Venue` (Место проведения, как VO или отдельная сущность), `SeatLayout` (План мест).
*   **Почему вместе?** План мест является неотъемлемой частью события. Нельзя продать билет на «воздух». Планировка мест меняется редко и связана жестко с конкретным событием.
*   **Инвариант:** Количество доступных мест >= 0.

**Агрегат 2: `Order` (Заказ)**
*   **Корень:** `Order`
*   **Состав:** `OrderLine` (Позиции заказа), `Money` (Стоимость).
*   **Почему вместе?** Заказ — это атомарная единица продажи. Либо все билеты в заказе забронированы, либо ни одного. Сумма заказа зависит от позиций.
*   **Ссылки:** `Order` содержит ID `Event` и ID `User`, но **не объекты** этих сущностей.

**Агрегат 3: `User` (Пользователь)**
*   **Корень:** `User`
*   **Состав:** `Profile`, `PaymentMethods`.
*   **Почему отдельно?** Пользователь может иметь много заказов. Заказы не должны знать все детали пользователя (например, адрес доставки не нужен для логики продажи, если билеты электронные).

#### Почему так?
Если бы `Event` и `Order` были одним агрегатом:
1.  Чтобы создать заказ, нам нужно загрузить весь `Event` (вместе со всем планом мест, который может быть гигантским).
2.  Если два пользователя одновременно покупают последний билет на одно событие, им пришлось бы блокировать весь объект `Event` (или `Order`, если он внутри). Это создаст «бутылочное горлышко» (bottleneck) для всех пользователей, покупающих билеты на разные события.
3.  Инвариант «Один билет нельзя продать дважды» должен проверяться атомарно.

---

### Типовая ошибка при выборе границ: «Мегатонный Агрегат» (The Big Ball of Mud Aggregate)

#### Ситуация:
Разработчик думает: *«Зачем разбивать? У меня есть товар, корзина, заказ, пользователь, склад. Они все связаны. Давайте сделаем один большой агрегат `SalesProcess`, чтобы было удобно»*.

**Структура «Ошибочного» Агрегата:**
*   Корень: `SalesProcess`
*   Внутренности: `User`, `Cart`, `Items`, `Inventory`, `PaymentInfo`, `ShippingAddress`.

#### Последствия (боли):

1.  **Проблема конкурентности (Concurrency):**
    *   Пользователь A редактирует свою корзину (меняет количество товара).
    *   Пользователь B пытается оплатить заказ.
    *   Поскольку всё это один агрегат, ORM (например, JPA/Doctrine) может попытаться заблокировать **весь** объект `SalesProcess` для записи.
    *   Результат: Пользователь A не может изменить количество товара, пока Пользователь B не закончит оплату. Или наоборот. Система тормозит.

2.  **Нарушение инкапсуляции:**
    *   Чтобы изменить адрес доставки в заказе, разработчику приходится загружать в память весь `User` со всеми его данными (пароль, старые заказы, настройки уведомлений), хотя для этого нужна только строка с адресом.
    *   В коде появляется доступ к «глубоким» вложениям: `salesProcess.user.address.street`. Это признак того, что инкапсуляция нарушена.

3.  **Сложные транзакции:**
    *   Любое изменение в этом агрегате приводит к проверке **всех** инвариантов. Изменили товар в корзине? Система проверяет, что пользователь активен, что склад не пуст, что оплата валидна. Это избыточно для простых операций.

4.  **Проблема N+1 и памяти:**
    *   При загрузке одного `SalesProcess` в память попадают десятки связанных сущностей. Если на странице списка заказов 100 записей, вы загрузите 100 полных копий пользователей, складов и т.д. Память сервера уйдет на нет.

#### Как исправить?

Разбить на мелкие агрегаты:
1.  **`Cart`** (Корзина): Хранит ID товаров и количества. Атомарность: «Количество > 0».
2.  **`Inventory`** (Склад): Хранит остатки. Атомарность: «Остаток >= 0».
3.  **`Order`** (Заказ): Хранит итог. Атомарность: «Сумма позиций = итоговая сумма».

**Взаимодействие:**
*   Когда пользователь подтверждает корзину (`Cart` -> `Order`), сервис вызывает метод на `Inventory`: `reserveQuantity(itemId, qty)`.
*   `Inventory` сам проверяет, хватит ли товара. Если да — резервирует. Если нет — бросает исключение.
*   `Order` создается только после успешного резервирования.

Так мы получаем:
*   Маленькие, быстрые агрегаты.
*   Явные границы транзакций.
*   Легкую масштабируемость (можно вынести `Inventory` в отдельный сервис/микросервис, не трогая `Order`).

### Резюме

**Агрегат** — это инструмент управления **целостностью данных** и **конкурентностью**.
*   **Определяйте границы** там, где данные должны меняться атомарно.
*   **Избегайте** больших агрегатов, если только они действительно не требуют строгой атомарности для всех своих частей одновременно.
*   **Помните:** Ссылки (ID) между агрегатами — это нормально. Объекты внутри агрегата — это нормально. Объекты из других агрегатов внутри этого — это ошибка (кроме случаев, когда это Value Object, например, `Money` или `Address`, которые не имеют собственного жизненного цикла и идентичности).

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

Это классическая проблема **конкурентности (Concurrency)** в DDD. Если два процесса (например, два HTTP-запроса или два воркера из очереди) одновременно пытаются изменить один и тот же агрегат, происходит конфликт.

В PostgreSQL и ORM (Doctrine) это решается механизмом **Оптимистической блокировки (Optimistic Locking)**.

---

### 1. Что произойдет, если ничего не делать? (Проблема «Lost Update»)

Допустим, у нас есть агрегат `Counter` с полем `value = 100`.
Два процесса A и B одновременно читают его.

1.  **Process A:** Читает `value = 100`.
2.  **Process B:** Читает `value = 100`.
3.  **Process A:** Выполняет логику: `value = value + 10` (теперь 110). Сохраняет в БД.
4.  **Process B:** Выполняет логику: `value = value - 5` (на основе старой версии, теперь 95). Сохраняет в БД.
5.  **Результат:** В БД лежит `95`.
6.  **Бизнес-потеря:** Операция A (+10) «пропала» без вести. Система считает, что всё прошло успешно, но данные неверны.

Это называется **Lost Update Problem**.

---

### 2. Как проектируем с учетом этого?

Мы используем **Версионирование (Versioning)**. Это стандартный подход в DDD и ORM.

#### Шаг 1: Добавляем поле версии в Агрегат

В доменную модель `Counter` (или любой другой Entity) добавляем приватное поле `$version`.

```php
final class Counter
{
    private int $id;
    private int $value;
    private int $version; // Новое поле

    public function increment(int $amount): void
    {
        $this->value += $amount;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    // Внутренний метод, вызывается ORM при сохранении
    public function bumpVersion(): void
    {
        $this->version++;
    }
}
```

#### Шаг 2: Настройка маппинга в Doctrine

В Entity (или в самом доменном классе, если он является Entity) указываем, что поле `version` управляется версионированием.

```php
#[ORM\Entity]
class Counter
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $value;

    // Ключевая аннотация/атрибут:
    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER)]
    private int $version = 0;

    // ... методы increment, getVersion, bumpVersion ...
}
```

#### Шаг 3: Механизм работы (Optimistic Locking)

Когда вы вызываете `$em->flush()`:

1.  **SELECT:** Doctrine загружает сущность из БД. Она видит `version = 5`.
2.  **Изменение:** Вы меняете `$counter->increment(10)`.
3.  **UPDATE:** При `flush()` Doctrine генерирует SQL:
    ```sql
    UPDATE counters 
    SET value = 110, version = 6 
    WHERE id = 1 AND version = 5;
    --                                    ^^^^^^^^^^^^
    --                          Вот тут проверка версии!
    ```
4.  **Сценарий успеха:**
    *   Если в БД `version` все еще `5` (никто не менял), UPDATE затрагивает 1 строку. Транзакция коммитится. Все хорошо.
5.  **Сценарий конфликта (Process B успел перед A):**
    *   Process B уже сохранил изменение, и в БД теперь `version = 6`.
    *   UPDATE от Process A: `UPDATE ... WHERE version = 5`.
    *   Строк не найдено (потому что версия уже 6).
    *   **Результат:** 0 строк затронуто.
    *   **Реакция ORM:** Doctrine (или драйвер DB) бросает исключение `OptimisticLockException` (или `StaleObjectException`).
    *   **Реакция вашего кода:** Вы ловите это исключение, логируете конфликт и, при необходимости, повторяете операцию (Retry) или сообщаете пользователю «Данные изменились, обновите страницу».

---

### 3. Как обрабатывать это в приложении (Application Layer)?

Вы не должны позволять `OptimisticLockException` упасть как 500 Internal Server Error. Это **ожиданное** событие.

#### Вариант 1: Ручное повторение (Retry Logic)

Если операция идемпотентна и короткая, можно повторить ее несколько раз.

```php
class CounterService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function increment(int $counterId, int $amount): void
    {
        $maxRetries = 3;
        
        for ($i = 0; $i < $maxRetries; $i++) {
            try {
                $counter = $this->em->find(Counter::class, $counterId);
                $counter->increment($amount);
                $this->em->flush(); // Здесь может случиться конфликт
                return; // Успех, выходим
            } catch (OptimisticLockException $e) {
                // Конфликт! Откатываем транзакцию
                $this->em->rollback();
                
                if ($i === $maxRetries - 1) {
                    throw new ConflictException('Too many concurrent updates, please try again.');
                }
                
                // Пауза перед следующим попыткой (Exponential Backoff)
                usleep(100000 * ($i + 1)); 
            }
        }
    }
}
```

#### Вариант 2: Перехват в Middleware/Controller (для REST API)

Если это HTTP-запрос, лучше просто вернуть пользователю **409 Conflict**.

```php
#[Route('/counters/{id}/increment', methods: ['POST'])]
public function increment(int $id, Request $request): Response
{
    $amount = $request->request->get('amount');
    try {
        $this->counterService->increment($id, $amount);
        return new Response('', 204);
    } catch (OptimisticLockException $e) {
        // Переводим техническое исключение в HTTP-статус
        return new JsonResponse(['error' => 'Resource was modified concurrently'], 409);
    }
}
```
*Почему 409?* Это стандартный HTTP-статус для «Conflict». Клиент (фронтенд или другой сервис) понимает, что нужно перечитать данные и попробовать снова.

---

### 4. Почему не использовать Пессимистическую блокировку (Pessimistic Locking)?

Пессимистическая блокировка — это `SELECT ... FOR UPDATE`. Она держит блокировку на строке в БД, пока транзакция не закончится.

**Почему в DDD мы чаще предпочитаем Оптимистическую?**

1.  **Масштабируемость:** Пессимистическая блокировка «замораживает» запись для других. Если транзакция долгая (медленный код), все остальные процессы зависнут. Оптимистическая блокировка не держит ресурсы, пока вы не сохраняете.
2.  **Гибкость:** Вы можете изменить состояние агрегата, проверить его, и только в конце проверить, не изменился ли он снаружи.
3.  **Чистота домена:** Оптимистическая блокировка является частью «контракта» сохранения. Пессимистическая требует явного управления блокировкой (`$em->lock($counter, LockMode::OPTIMISTIC, 5)`), что усложняет код сервиса.

**Когда использовать Пессимистическую?**
*   Если конфликты происходят **очень часто** (например, изменение баланса счета в банке), и стоимость отката/повтора высокая.
*   Если операция очень долгая (например, генерация PDF-отчета на 10 секунд), и вы хотите гарантировать, что данные не изменятся во время этой долгой операции.

---

### 5. Подводные камни

1.  **Забытый `version` в DQL:**
    Если вы делаете кастомный DQL-запрос, убедитесь, что вы не игнорируете поле `version`. Если вы обновляете данные напрямую через `UPDATE` в DQL, оптимистическая блокировка не сработает, потому что ORM не знает о том, что версия изменилась.
    *   *Решение:* Старайтесь обновлять через Entity (`$obj->setX(); $em->flush()`) или явно обновляйте версию в DQL.

2.  **Несогласованность внутри агрегата:**
    Если вы измените часть агрегата (например, добавите позицию в заказ), но не увеличите `version` корня — конфликт может не быть обнаружен.
    *   *Правило:* Любое изменение состояния агрегата должно приводить к увеличению `version` корня.

3.  **Идемпотентность при Ретрае:**
    Если вы повторяете операцию после конфликта, убедитесь, что она идемпотентна.
    *   *Пример:* `increment(10)` — идемпотентна (можно вызывать много раз, результат предсказуем).
    *   *Пример:* `createOrder()` — не идемпотентна (повторный вызов создаст два заказа). В таком случае, при конфликте лучше бросать ошибку, а не повторять.

---

### Итог

Для двух процессов, изменяющих один агрегат:

1.  Добавьте поле `version` в Entity.
2.  Пометьте его атрибутом `#[ORM\Version]`.
3.  При конфликте (0 строк затронуто при UPDATE) поймаете `OptimisticLockException`.
4.  Либо повторите операцию (если она короткая и идемпотентна), либо верните клиенту **409 Conflict**.

Это самый надежный, масштабируемый и «чистый» способ работы с конкурентностью в DDD-архитектурах.

> ⏱ 27.0s · prompt 36814 tok · output 1840 tok

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

**Кандидат:**

Доменные события (Domain Events) — это механизм коммуникации между частями домена, который позволяет сохранить инкапсуляцию агрегатов и избежать «цепочек вызовов» (Service A вызывает Service B, B вызывает C).

Однако именно **взаимодействие событий с транзакциями** — самое сложное и опасное место. Если сделать неверно, вы получите «фантомные» письма (отправлены, хотя заказ откатился) или потерянные события (заказ сохранен, а индекс не обновлен).

Давайте разберем это по частям.

---

### 1. Когда публикуем?

Доменное событие публикуется **внутри агрегатного корня** в момент, когда состояние агрегата изменилось, и это изменение имеет смысл для других частей системы.

**Правило:** Событие фиксирует *факт*, а не *действие*.
*   ✅ `OrderPlacedEvent` (Заказ оформлен).
*   ✅ `StockUpdatedEvent` (Остаток изменился).
*   ❌ `SendEmailEvent` (Это техническая деталь, а не доменный факт).

**Код в домене:**
```php
class Order
{
    private array $occurredEvents = [];

    public function place(): void
    {
        if ($this->items->isEmpty()) {
            throw new DomainException('Cannot place empty order');
        }

        $this->status = Status::Placed;
        
        // Регистрируем событие ВНУТРИ агрегата
        $this->registerEvent(new OrderPlacedEvent($this->id, $this->total));
    }

    /**
     * Возвращает накопленные события и очищает список
     * (обычно вызывается при сохранении)
     */
    public function pullEvents(): array
    {
        $events = $this->occurredEvents;
        $this->occurredEvents = [];
        return $events;
    }

    private function registerEvent(object $event): void
    {
        $this->occurredEvents[] = $event;
    }
}
```

---

### 2. Как обрабатываем?

В Symfony (и других фреймворках) события обрабатываются **Listener'ами** (слушателями).

1.  Агрегат сохраняется в БД.
2.  После успешного сохранения (flush + commit) сервис извлекает события из агрегата.
3.  Сервис диспатчит (отправляет) их в EventBus.
4.  EventBus находит всех слушателей, подписанных на этот тип события.
5.  Слушатели выполняют свою работу (отправка письма, обновление индекса, запись в аналитику).

**Код в Application Layer (Сервис):**
```php
class OrderService
{
    public function __construct(
        private OrderRepository $repo,
        private EventDispatcherInterface $eventBus,
        private EntityManagerInterface $em
    ) {}

    public function placeOrder(int $orderId): void
    {
        $order = $this->repo->find($orderId);
        
        // 1. Изменяем состояние домена
        $order->place(); 

        // 2. Сохраняем в БД (Транзакция!)
        $this->em->flush(); 
        $this->em->clear(); // Важно: отделить доменную модель от UoW

        // 3. Извлекаем и диспатчим события ТОЛЬКО ПОСЛЕ успешного сохранения
        $events = $order->pullEvents();
        foreach ($events as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
```

---

### 3. Критический момент: Связь с транзакциями БД

Здесь кроются главные грабли. У нас есть два сценария:

#### Сценарий А: «Outbox Pattern» (Рекомендуемый для надежности)
Событие сохраняется в ту же транзакцию, что и данные.

1.  `Order` сохраняется в таблицу `orders`.
2.  Одновременно в таблицу `domain_events` (Outbox) записывается запись: `event_type: OrderPlaced, payload: {...}, status: pending`.
3.  **Commit** транзакции.
4.  Фоновый воркер (Poller) опрашивает таблицу `domain_events`, находит `pending` события и отправляет их в Message Queue (RabbitMQ/Kafka) или напрямую вызывает хендлеры.
5.  После успешной отправки статус события меняется на `processed`.

**Плюсы:**
*   **ACID:** Либо заказ и событие сохраняются оба, либо оба откатываются.
*   **Надежность:** Если приложение упадет сразу после commit, но до отправки в MQ, воркер найдет событие в таблице и отправит его позже.
*   **Идемпотентность:** Воркер может безопасно повторять отправку.

**Минусы:**
*   Сложнее архитектура (нужна таблица Outbox и воркер-поллер).
*   Есть небольшая задержка между сохранением данных и обработкой события.

#### Сценарий Б: «Immediate Dispatch» (Простой, но рискованный)
Событие диспатчится сразу после `flush()`, но в рамках той же транзакции (или сразу после commit).

**Проблема «Рискованности»:**
1.  `$order->place()` — данные в памяти.
2.  `$em->flush()` — SQL-команды отправлены в БД, но транзакция еще не закоммичена? Или уже закоммичена?
    *   Если `dispatch` происходит **до** commit: Событие обработано, письмо отправлено. Затем commit падает (например, deadlock). Транзакция откатывается. **Итог:** Письмо отправлено, а заказа в БД нет. *Баг!*
    *   Если `dispatch` происходит **после** commit: Заказ сохранен. Событие диспатчено. Письмо отправляется. Затем сервер падает, пока воркер обрабатывает письмо. **Итог:** Письмо может не дойти, но заказ есть. *Меньший баг, но все равно потеря функциональности.*

**Как решить в Symfony?**
Symfony Messenger позволяет использовать **Transport** (очередь).
1.  `dispatch()` кладет сообщение в таблицу `messenger_messages` (если транспорт DB) или в RabbitMQ.
2.  Если транспорт DB и это происходит в рамках одной транзакции с бизнес-данными — это работает как Outbox.
3.  Если транспорт RabbitMQ — сообщение уходит в MQ почти мгновенно. Если потом commit упадет, сообщение уже в MQ, а данных нет.

**Рекомендация:**
Для критических бизнес-процессов (деньги, инвентарь) используйте **Outbox Pattern** или убедитесь, что ваш EventBus работает с DB-транспортом внутри той же транзакции.

---

### 4. Какие проблемы решает этот подход?

1.  **Разделение ответственности (Loose Coupling):**
    `Order` не знает, что после его создания нужно отправить письмо или обновить Elasticsearch. Он просто говорит: «Я создан».
    `EmailService` и `SearchService` подписываются на событие. Если завтра мы добавим `AnalyticsService`, нам не нужно менять код `Order`.

2.  **Расширяемость (Open/Closed Principle):**
    Легко добавляются новые потребители событий без изменения существующего кода.

3.  **Асинхронность:**
    Письмо и индексация выполняются позже, не тормозя ответ пользователю (если использовать очереди).

4.  **Аудит и Трассировка:**
    История событий (`OrderCreated`, `OrderPaid`, `OrderShipped`) дает полную картину жизненного цикла заказа. Это полезно для отладки и поддержки.

---

### 5. Какие проблемы создает этот подход?

1.  **Eventual Consistency (Событийная согласованность):**
    Вы не можете ожидать, что после сохранения заказа данные сразу появятся в поисковом индексе. Между `Save` и `Index Update` есть задержка (миллисекунды или секунды).
    *   *Решение:* Пользовательская UI должна容忍 (tolerate) эту задержку (показать «Обновляется...» или использовать оптимистичный UI).

2.  **Порядок событий:**
    Если один сервис генерирует два события подряд (`A`, `B`), а другие сервисы обрабатывают их параллельно в разных очередях, порядок `A -> B` может нарушиться.
    *   *Решение:* Для связанных событий использовать одну очередь/партицию. Или использовать `Partition Key` (например, `OrderId`), чтобы все события одного заказа шли в один воркер последовательно.

3.  **Дубликаты (Duplicates):**
    Если MQ переживает сбой сети, она может доставить событие дважды.
    *   *Решение:* Хендлеры событий должны быть **Идемпотентными**.
        ```php
        public function onOrderPlaced(OrderPlacedEvent $event): void {
            // Проверка: уже отправляли письмо на этот OrderId?
            if ($this->emailLogRepo->exists($event->orderId)) {
                return;
            }
            // Отправляем письмо
        }
        ```

4.  **Сложность отладки:**
    Логика разбросана по разным классам (Event, Dispatcher, Listener). Трассировка ошибки «почему письмо не пришло?» требует знания всей цепочки.
    *   *Решение:* Хорошее логирование (Correlation ID, Trace ID), который пробивает все этапы.

---

### Итоговая рекомендация

1.  **Генерируйте события** внутри агрегатных корней при изменении состояния.
2.  **Храните события** (Outbox) в БД в рамках той же транзакции, что и бизнес-данные, если это критично.
3.  **Отправляйте** их в очередь (RabbitMQ/Kafka/Redis) или напрямую в обработчики из фоновой задачи.
4.  **Обеспечьте идемпотентность** обработчиков (хендлеров).
5.  **Не блокируйте** основной поток ожидания пользователя тяжелыми операциями (отправка писем) — давайте им время.

Это превращает вашу систему из «спящего монолита» в живую, отзывчивую и легко расширяемую архитектуру событий.

> ⏱ 35.6s · prompt 41173 tok · output 2428 tok

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

**Кандидат:**

Это фундаментальный вопрос DDD, от ответа на который зависит вся архитектура вашей доменной модели. Ошибки в классификации (вызвать Entity там, где нужен VO, или наоборот) приводят к лишнему коду, сложным запросам и нарушению инкапсуляции.

Давайте разберем это по частям.

---

### 1. Entity vs Value Object: Ключевые отличия

Главное различие — **Идентичность (Identity)**.

| Характеристика | **Entity (Сущность)** | **Value Object (Объект-значения)** |
| :--- | :--- | :--- |
| **Идентификатор** | Да. Имеет уникальный ID (GUID, UUID, автоинкремент). | Нет. Идентификатор не нужен. |
| **Равенство** | Два объекта равны, только если имеют **один и тот же ID**. <br> `if ($userA->id === $userB->id) { // equal }` | Два объекта равны, если **все их поля идентичны**. <br> `if ($moneyA->amount === $moneyB->amount && $moneyA->currency === $moneyB->currency) { // equal }` |
| **Жизненный цикл** | Долгий. Создается, изменяется (мутирует), удаляется. | Короткий. Создается, используется, выбрасывается. Не изменяется. |
| **Изменяемость** | Обычно изменяема (Mutable). | **Неизменяема (Immutable)** всегда. |
| **Хранение в БД** | Всегда в отдельной таблице (или колонках с FK). | Часто хранится внутри Entity как JSON, строка или отдельные колонки. Может дублироваться. |
| **Примеры** | `User`, `Order`, `Product`, `Invoice`. | `Money`, `Address`, `Email`, `DateRange`, `PhoneNumber`, `Slug`. |

#### Почему это важно?
*   **Entity** требует управления состоянием: «Кто я?», «Что со мной делали?».
*   **Value Object** отвечает на вопрос: «Какое у меня значение?». Если значение правильное — объект валиден. Если нет — бросаем исключение при создании.

---

### 2. Как решить, что это: Entity или VO?

Задайте себе 3 вопроса:

#### Вопрос 1: «Есть ли смысл отслеживать историю изменений этого объекта?»
*   **Да:** Это **Entity**.
    *   *Пример:* `User`. Нам важно знать, когда он зарегистрировался, когда менял пароль, когда входил. ID нужен, чтобы связать эти события.
    *   *Пример:* `Order`. Мы следим за статусами: Created -> Paid -> Shipped -> Completed.
*   **Нет:** Это **Value Object**.
    *   *Пример:* `Money`. Нам не важно, «какой именно» доллар мы держим в руках. Если у меня есть 100$, и у вас есть 100$, это одинаковые значения. Нам не нужно знать ID этого доллара.
    *   *Пример:* `Address`. Если пользователь меняет адрес, мы не пишем «Старый адрес удален, новый создан». Мы просто меняем значение поля `address` у сущности `User` (или `Order`).

#### Вопрос 2: «Могу ли я заменить этот объект на другой без потери смысла, если значения совпадают?»
*   **Да:** Это **Value Object**.
    *   *Пример:* У заказа есть `Money` (стоимость 100$). Если я создам новый объект `Money(100, USD)` и подставлю его вместо старого — ничего не изменится. Система этого не заметит и не сломается.
*   **Нет:** Это **Entity**.
    *   *Пример:* У заказа есть ссылка на `User`. Если я заменю ссылку на *другого* User с тем же именем, но другим ID — это совсем другой клиент. Смысл заказа изменится.

#### Вопрос 3: «Нужен ли этому объекту собственный жизненный цикл (create, update, delete)?»
*   **Да:** Это **Entity**.
*   **Нет:** Это **Value Object**. VO не живут сами по себе. Они существуют только как часть какой-то Entity или в составе другого VO.

---

### 3. Unified Language (Единый язык / Ubiquitous Language)

**Единый язык** — это договоренность между разработчиками и бизнес-экспертами о том, как называть вещи.

В DDD это критически важно, потому что **код должен читаться как бизнес-процесс**.

#### Пример проблемы без Единого языка:
*   Бизнес говорит: «Покупатель оформляет покупку».
*   Разработчик 1 пишет: `class Customer { public function buy() {} }`
*   Разработчик 2 пишет: `class Buyer { public function purchase() {} }`
*   Разработчик 3 пишет: `class Client { public function order() {} }`

В базе данных:
*   Таблица `customers`
*   Таблица `purchases`
*   Таблица `orders`

Через полгода никто не понимает, что такое `Customer` в контексте отчета.

#### Пример с Единым языком:
Все согласились, что:
1.  Тот, кто покупает, называется **Customer** (Клиент).
2.  Процесс покупки называется **Placing an Order** (Оформление заказа).
3.  Результат процесса называется **Order** (Заказ).

Теперь код выглядит так:
```php
class Customer { ... }

class Order {
    public function __construct(private Customer $customer) {}
    
    public function place(): void {
        // Логика оформления
    }
}

// В контроллере:
$customer->placeOrder($items); 
```

**Как это влияет на код?**
1.  **Имена классов и методов** берутся напрямую из доменной глоссарии.
2.  **Нет транслита:** Не бывает `User`, `Item`, `Product` без уточнения контекста. Лучше `Customer`, `OrderLineItem`, `ProductVariant`.
3.  **Документация:** Единый язык становится частью документации. Если вы видите непонятное слово в коде — вы идете спрашивать у бизнес-аналитика, что оно значит, а не гадаете.

---

### 4. Типовые ошибки в выборе Entity vs VO

#### Ошибка 1: «Жирные» Value Objects
Вы делаете `Address` сущностью, потому что «хочу обновлять его отдельно».
*   *Последствия:* Нужно создавать отдельную таблицу `addresses`, хранить FK в `users`. При изменении адреса пользователя нужно делать UPDATE. При удалении пользователя — CASCADE DELETE из `addresses`. Это избыточно, если адрес — просто данные.
*   *Решение:* Сделайте `Address` Value Object. Храните его в JSON-колонке или отдельных полях `users`.

#### Ошибка 2: «Бедные» Entities
Вы делаете `User` Value Object, потому что «у него нет сложной логики».
*   *Последствия:* Вы не можете отслеживать, когда пользователь был создан, когда верифицирован email. Вы не можете сделать уникальность по email через FK. Вы теряете возможность связывать действия с конкретным пользователем по ID.
*   *Решение:* `User` — это всегда Entity.

#### Ошибка 3: Мутация Value Objects
```php
class Money {
    public function __construct(public float $amount) {}
    
    public function add(float $amount): void {
        $this->amount += $amount; // Плохо! Мутация
    }
}

// Использование:
$money->add(100); 
$otherMoney->add(50);
// Если где-то в коде был баг, $money может быть испорчен.
```
*   *Решение:*
```php
class Money {
    private float $amount;
    
    public function __construct(private float $amount) {}
    
    public function add(float $amount): self {
        return new self($this->amount + $amount); // Возвращаем НОВЫЙ объект
    }
}

// Использование:
$money = $money->add(100); 
$otherMoney = $otherMoney->add(50);
// $money и $otherMoney — разные объекты, старые значения не потеряны.
```

### Итог

1.  **Entity:** Идентична по ID, имеет жизненный цикл, изменяема. Примеры: `User`, `Order`.
2.  **Value Object:** Идентична по значениям, неизменяема, не имеет ID. Примеры: `Money`, `Email`, `Address`.
3.  **Единый язык:** Код должен использовать термины, понятные бизнесу. `Customer.placeOrder()`, а не `Client.buyStuff()`.

Если сомневаетесь, спросите себя: *«Нужно ли мне знать, КАКОЙ именно это объект, или достаточно знать, КАКОЕ он имеет значение?»*
*   Если «какой» — **Entity**.
*   Если «какое значение» — **Value Object**.

> ⏱ 31.2s · prompt 43656 tok · output 2100 tok

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

**Кандидат:**

Структура приложения по принципам Чистой Архитектуры (Clean Architecture) в PHP — это способ защитить ваш домен от изменений во фреймворке (Symfony), базе данных (Doctrine) и внешнем мире (API, очереди).

Главное правило: **Зависимости направлены только внутрь.**
*   Внешний мир (UI, БД, API) знает про Домен.
*   Домен **не знает** ни про UI, ни про БД, ни про фреймворк.

Давайте разберем слои, их роль и где разместить конкретные компоненты.

---

### 1. Структура слоёв (Изнутри наружу)

#### Слой 1: **Domain (Домен)**
*   **Что здесь:** Сущности (`User`, `Order`), Объекты-значения (`Money`, `Email`), Доменные Сервисы (`PricingCalculator`), Доменные Интерфейсы (Ports), Доменные События (`OrderPlacedEvent`).
*   **Зависимости:** НЕТ. Только PHP. Здесь нет `use Symfony\...`, нет `use Doctrine\...`.
*   **Цель:** Чистая бизнес-логика. Если вы перенесете этот код на Java или Go, он должен остаться почти без изменений.

#### Слой 2: **Application (Приложение / Use Cases)**
*   **Что здесь:** Обработчики команд (Use Cases), Координаторы, DTOs (Data Transfer Objects), Интерфейсы для внешних зависимостей (Repository Interfaces, Event Dispatcher Interfaces), которые реализуются снаружи.
*   **Зависимости:** Зависит от Domain. Не зависит от Infrastructure.
*   **Цель:** Организация бизнес-процессов. Здесь происходит оркестрация: «Загрузи пользователя, измени состояние, сохрани, отправь событие».

#### Слой 3: **Infrastructure (Инфраструктура)**
*   **Что здесь:** Реализации репозиториев (Doctrine Entities, QueryBuilders), Адаптеры внешних сервисов (MailChimpClient, RedisCache), Связь с очередями (RabbitMQ Publisher), Файловая система.
*   **Зависимости:** Зависит от Domain и Application (реализует их интерфейсы).
*   **Цель:** Техническая реализация. «Грязная» работа с базами, сетью, дисками.

#### Слой 4: **Interface / Presentation (Интерфейс / UI)**
*   **Что здесь:** Symfony Controllers, REST Endpoints, CLI Commands, Job Handlers (обработчики очередей), Twig-шаблоны (или API-ответы).
*   **Зависимости:** Зависит от Application (вызывает Use Cases). Не знает деталей Domain напрямую (обычно).
*   **Цель:** Взаимодействие с пользователем. Парсинг HTTP-запросов, валидация входных данных (Form), формирование ответа.

---

### 2. Куда попадает конкретный код?

#### ❓ Куда попадает Symfony Controller?
**В слой Interface (Presentation).**

*   Controller **не содержит бизнес-логики**.
*   Его задача:
    1.  Получить данные из HTTP-запроса (`Request`).
    2.  Провалидировать их (Form или Validator).
    3.  Создать **Command** (DTO) из этих данных.
    4.  Вызвать **Use Case** (Application Layer).
    5.  Сформировать HTTP-ответ (`Response`).

```php
// App/Controller/OrderController.php (Слой Interface)
class OrderController extends AbstractController
{
    public function __construct(private CreateOrderUseCase $createOrder) {} // Зависит от Application

    #[Route('/orders', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        // 1. Парсинг и валидация (UI concerns)
        $form = $this->createForm(OrderCreateType::class);
        $form->handleRequest($request);
        
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->json(['errors' => $form->getErrors()], 400);
        }

        // 2. Преобразование Form-данных в Command (DTO)
        $command = new CreateOrderCommand(
            userId: $this->getUser()->getId(),
            items: $form->get('items')->getData()
        );

        // 3. Вызов бизнес-логики
        $orderId = $this->createOrder->execute($command);

        // 4. Формирование ответа
        return $this->json(['id' => $orderId], 201);
    }
}
```

#### ❓ Куда попадает Doctrine?
**В слой Infrastructure.**

*   **Entity (в строгом DDD):** Часто выделяют отдельные классы `OrderEntity` (Infra) и `Order` (Domain).
    *   `OrderEntity` имеет аннотации `#[ORM\Entity]`, свойства `private int $id`, методы для Doctrine.
    *   `Order` (Domain) — чистый PHP-класс с бизнес-методами.
    *   *Адаптер (Mapper):* Класс, который переводит `Order` <-> `OrderEntity`.
*   **Repository Implementation:**
    ```php
    // App/Infrastructure/Doctrine/OrderRepository.php (Слой Infra)
    class OrderRepository implements OrderRepositoryInterface // Интерфейс из Application/Domain
    {
        public function __construct(private EntityManagerInterface $em) {}

        public function find(int $id): ?Order
        {
            $entity = $this->em->find(OrderEntity::class, $id);
            if (null === $entity) return null;
            
            // Маппинг Entity -> Domain Object
            return OrderMapper::toDomain($entity); 
        }

        public function save(Order $order): void
        {
            // Маппинг Domain -> Entity
            $entity = OrderMapper::toEntity($order);
            $this->em->persist($entity);
            $this->em->flush();
        }
    }
    ```

#### ❓ Куда попадает обработчик очереди (Job Handler)?
**В слой Interface (Presentation) или отдельный слой "Entrypoint".**

*   В чистой архитектуре входные точки (HTTP, CLI, Queue, WebSocket) считаются частью **Presentation Layer**.
*   Обработчик очереди делает то же самое, что и Controller:
    1.  Получает payload (сообщение из очереди).
    2.  Преобразует в Command/DTO.
    3.  Вызывает Use Case.
    4.  (Опционально) Обработывает ошибки и логирует.

```php
// App/Messenger/Handler/SendEmailHandler.php (Слой Interface/Entrypoint)
class SendEmailHandler
{
    public function __construct(private SendEmailUseCase $useCase) {}

    public function __invoke(SendEmailMessage $message): void
    {
        // Message из очереди превращаем в Command
        $command = new SendEmailCommand(
            to: $message->email,
            subject: $message->subject,
            body: $message->body
        );

        // Вызываем бизнес-логику
        $this->useCase->execute($command);
    }
}
```

---

### 3. Правило зависимостей (Dependency Rule)

Векторы зависимостей (стрелки кода `use`):

```
Interface (Controller/Job) 
       ↓ зависит от
Application (Use Cases) 
       ↓ зависит от
Domain (Entities/Services)
```

**Infrastructure** реализует интерфейсы, объявленные в **Application** или **Domain**, и зависит от **Domain**.

**Никогда:**
*   `Domain` не использует `Symfony\Component\HttpFoundation\Request`.
*   `Domain` не использует `Doctrine\ORM\EntityManagerInterface`.
*   `Application` не знает, что `Repository` работает через SQL или через REST API. Он знает только интерфейс `OrderRepositoryInterface`.

---

### 4. Как следить, что границы не нарушаются?

Если разработчики начнут писать `use Doctrine\...` в классе `Order` (Domain), архитектура рассыпается. Вот инструменты контроля:

#### 1. Статический анализатор: PHPStan / Psalm
Настройте уровень строгости (level 8-9). Они не знают о DDD напрямую, но помогут поймать ошибки типов.

#### 2. Arch (или подобные) — Архитектурные тесты
Это юнит-тесты, которые проверяют структуру кода. Библиотека `shipmonk/rules` или написанные вручную с использованием `phpstan/extension-installer`.

Пример правила: *«В namespace `App\Domain` не должно быть импортов из `App\Infrastructure` или `Symfony\*`»*.

```php
// Пример концепции тест-правилы (псевдокод для Arch)
assert:
    - rule: 
        target: App\Domain
        mustNotUse:
            - Symfony\Component
            - Doctrine\ORM
            - App\Infrastructure
```

#### 3. Физическое разделение директорий + PSR-4
*   `src/Domain/` — только PHP.
*   `src/Application/` — зависит от `Domain`.
*   `src/Infrastructure/` — зависит от `Domain` и `Application`.
*   `src/Interface/` — зависит от `Application`.

Если разработчик случайно импортирует `EntityManagerInterface` в `src/Domain/Order.php`, IDE может подсказать, что класс из другого слоя (если настроены правила), а линтер (PHPStan) с кастомными правилами выкинет ошибку.

#### 4. DI-контейнер (Symfony)
*   Используйте **Autowiring** и **Tags**.
*   В `services.yaml` настройте, чтобы сервисы из `App\Domain` были `public: false` (или вообще не регистрировались напрямую, если они не зависят от DI).
*   Внедряйте только **интерфейсы** в Use Cases.
    ```yaml
    services:
        App\Application\CreateOrderUseCase:
            arguments:
                $orderRepository: '@App\Infrastructure\Doctrine\OrderRepository' # Реализация из Infra
    ```
*   Если вы попытаетесь внедрить конкретный класс из Infra в класс из Domain — контейнер может не найти его, или вы получите ошибку, потому что Domain не знает о существе этого класса.

---

### Итоговая картина

| Компонент | Слой | Почему |
| :--- | :--- | :--- |
| `Order` (Entity) | **Domain** | Бизнес-логика, правила. |
| `OrderEntity` | **Infrastructure** | Маппинг в БД, аннотации Doctrine. |
| `OrderRepositoryInterface` | **Domain** или **Application** | Контракт: "Дай мне заказ". |
| `DoctrineOrderRepository` | **Infrastructure** | Реализация: "Забираю из БД". |
| `CreateOrderUseCase` | **Application** | Оркестрация: "Сделай все шаги". |
| `OrderController` | **Interface** | HTTP: "Прими JSON, передай в UseCase". |
| `SendOrderEmailHandler` | **Interface** | Queue: "Прими сообщение, передай в UseCase". |
| `MailgunClient` | **Infrastructure** | Сеть: "Говорит с API Mailgun". |

Эта структура позволяет вам заменить Doctrine на Eloquent (Laravel), Symfony на Slim, а Redis на Memcached, **не трогая** код вашего Домена и Бизнес-логики. Это и есть главная ценность чистой архитектуры.

> ⏱ 39.5s · prompt 45825 tok · output 2663 tok

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

**Кандидат:**

Для Symfony-приложения с PostgreSQL я выбираю **адаптивную пирамиду тестов**, которая делает упор на **быстрые юнит-тесты бизнес-логики** и **стабильные интеграционные тесты критических путей**.

Главный принцип: **Максимум покрытия бизнес-логики, минимум покрытия «склейки» фреймворка.**

---

### 1. Пирамида тестов (Чем ниже, тем быстрее и больше)

#### 🔽 Уровень 1: Юнит-тесты (Unit Tests)
*   **Что тестируем:** Чистый код. Доменные классы (`Order`, `Money`), сервисы бизнес-логики, валидаторы, мапперы.
*   **Что мокаем:** Всё, что находится за границей тестируемого класса (репозитории, внешние API, очереди).
*   **Скорость:** Миллисекунды.
*   **Количество:** 50–70% от всех тестов.
*   **Инструмент:** PHPUnit + Mockery/Prophecy.

#### 🔼 Уровень 2: Интеграционные тесты (Integration Tests)
*   **Что тестируем:** Взаимодействие компонентов. Controller -> Service -> Doctrine -> PostgreSQL. Валидация форм, безопасность (Auth), маршрутизация.
*   **Что мокаем:** Только внешние системы (другие микросервисы, платёжные шлюзы), но **НЕ** базу данных (мы используем реальную или кэшированную БД).
*   **Скорость:** Секунды (из-за загрузки kernel и БД).
*   **Количество:** 20–30% от всех тестов.
*   **Инструмент:** Symfony `WebTestCase`, `KernelTestCase`.

#### 🔺 Уровень 3: E2E / API-тесты (End-to-End)
*   **Что тестирует:** Полный сценарий «Пользователь делает X, система отвечает Y».
*   **Что мокаем:** Почти ничего. Всё реально.
*   **Скорость:** Десятки секунд/минут.
*   **Количество:** 5–10% от всех тестов.
*   **Инструмент:** Postman/Newman, Cypress (для фронтенда+бэкенд), или просто curl в CI.

---

### 2. Что мокаем, а что нет?

Здесь важно понять границу между **логикой** и **инфраструктурой**.

#### ✅ МОКАЕМ (Заменяем двойниками):
1.  **Внешние API:** Stripe, Mailgun, Elasticsearch. Мы не хотим ждать реального ответа или зависеть от их доступности в CI.
    *   *Как:* Интерфейс `PaymentGatewayInterface` -> `MockPaymentGateway`.
2.  **Очереди (Messenger):** Мы не хотим, чтобы при тесте контроллера реально создавалось сообщение в таблице `messenger_messages` (если это мешает изоляции).
    *   *Как:* Мокнуть `MessageBusInterface` или использовать транспорт `in-memory` (для юнит-тестов) и `doctrine` (для интеграционных).
3.  **Время (`Clock`):** Если ваша логика зависит от текущей даты (скидки, сроки), мокаем `Clock` интерфейс.

#### ❌ НЕ МОКАЕМ (Используем реальные компоненты):
1.  **База данных (PostgreSQL):**
    *   *Почему:* Моканние репозитория Doctrine скрывает ошибки маппинга (N+1, неправильные JOIN'ы, типы данных). Мы хотим видеть реальный SQL.
    *   *Но:* Мы используем **отдельную базу данных для тестов** (через Docker Compose в CI).
2.  **Доменные классы:**
    *   `Money`, `Order`, `User` — это чистый PHP. Их не нужно мокать, их нужно использовать.
3.  **Формы и Валидаторы (Symfony):**
    *   Они часть логики приложения. Если форма не работает, это баг. Тестируем её интеграционно (через `WebTestCase`).

---

### 3. Как поддерживать тесты быстрыми?

#### 1. Изоляция тестов (Parallelization)
Используйте `PHPUnit --processes 4` или `PHPUnit --processes auto`.
*   Каждый процесс получает свою «чистую» среду (если тесты изолированы).
*   Для Symfony это работает, если вы используете `WebTestCase` правильно (каждый тест получает новый Kernel, но кэш может быть общим — здесь помогает `static::createKernel()` с уникальным кэшем или очистка перед запуском).

#### 2. Быстрая очистка БД
Вместо `TRUNCATE TABLE` (который медленный и блокирующий) используйте:
*   **`ORM SchemaTool::dropSchemaAndCreate()`:** Медленно при первом запуске, быстро при повторных (если схема не меняется).
*   **Лучший вариант:** В CI/CD поднимайте **новый контейнер PostgreSQL** перед запуском тестов. Это гарантирует чистоту и высокую скорость (так как индексы уже построены, а таблица пуста).

#### 3. Избегайте `WebTestCase` там, где можно обойтись `KernelTestCase`
*   `WebTestCase` загружает весь HTTP-стек (Router, Controller, Twig, Security).
*   Если вы тестируете только сервис, используйте `KernelTestCase`, чтобы не запускать HTTP-слой.

#### 4. Фикстуры вместо SQL-скриптов
Используйте библиотеки вроде `zenstruck/foundry` (на базе Doctrine Fixtures).
*   Они позволяют создавать объекты через код: `User::factory()->create(['email' => 'test@example.com'])`.
*   Быстрее, чем ручные INSERT'ы, и легче читать.

---

### 4. Как поддерживать тесты стабильными (Flaky-устойчивыми)?

#### 1. Детерминизм
*   **Время:** Никогда не используйте `date()` или `new \DateTime()` внутри бизнес-логики. Внедряйте `Clock` интерфейс. В тестах передавайте фиксированную дату.
*   **Случайность:** Если используете `rand()`, сидите генератор (`mt_srand(42)`) или используйте фиксированные значения в тестах.
*   **Порядок выполнения:** PHPUnit по умолчанию выполняет тесты в порядке файлов. Убедитесь, что тесты не зависят от состояния, оставленного предыдущим тестом (каждый тест должен быть self-contained).

#### 2. Обработка конкурентности
*   Если тест проверяет оптимистическую блокировку, убедитесь, что вы точно знаете, какая версия сущности будет в БД перед тестом.
*   Используйте `em->flush()` явно в точках, где вы ожидаете изменения в БД.

#### 3. Логирование и Отладка
*   Если тест падает непонятно, выведите SQL-запросы.
    *   В Symfony: `#[Test] public function testOrderCreation() { ... }`
    *   Добавьте в конфиг тестовой среды логирование SQL: `doctrine.dbal.logging.enabled: true`.
*   Используйте `dump()` или `var_dump()` (или лучше `phpunit-logger`) для промежуточных значений.

#### 4. Изоляция от внешних зависимостей
*   Если тест падает, потому что SMTP-сервер не отвечает — это плохой дизайн теста.
*   Всегда используйте транспорт `in-memory` или мокну SMTP-клиент в тестах, которые не проверяют именно отправку письма.

---

### 5. Пример структуры тестов

```
tests/
├── Unit/
│   ├── Domain/
│   │   ├── OrderTest.php       # Тестируем логику Order
│   │   └── MoneyTest.php       # Тестируем арифметику Money
│   └── Application/
│       └── CreateOrderUseCaseTest.php # Тестируем оркестрацию (мок репо)
├── Integration/
│   ├── Controller/
│   │   └── OrderControllerTest.php # Тестируем HTTP -> Service -> DB
│   └── Repository/
│       └── OrderRepositoryTest.php # Тестируем реальные SQL-запросы
└── Functional/
    └── FullOrderFlowTest.php   # Сценарий: Логин -> Корзина -> Оплата
```

### Итоговая рекомендация

1.  **Пишите юнит-тесты** для всей бизнес-логики (домен, сервисы). Это самые важные тесты.
2.  **Пишите интеграционные тесты** для контроллеров и ключевых сценариев с БД.
3.  **Мокните** только внешние мира (API, время, очереди).
4.  **Не мокните** базу данных в интеграционных тестах.
5.  **Используйте Docker** для PostgreSQL в CI, чтобы тесты были быстрыми и изолированными.
6.  **Проведите границы:** Домен не знает про Symfony. Инфраструктура не знает про Бизнес-правила. Тесты должны отражать эту структуру.

Такой подход даст вам уверенность в коде, быструю обратную связь при разработке и стабильное CI-окружение.

> ⏱ 32.6s · prompt 48544 tok · output 2152 tok

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

**Кандидат:**

Когда два пользователя (или процесса) одновременно изменяют одну и ту же запись, мы сталкиваемся с классической проблемой **конкурентности (Concurrency)**. Если не принять мер, данные могут «затереться» друг друга (Lost Update), нарушить бизнес-правила или привести к перерасходу ресурсов (например, двойное списание денег).

Вот полный разбор проблем, способов защиты и выбора стратегии.

---

### 1. Какие проблемы могут возникнуть?

1.  **Lost Update (Потерянное обновление):**
    *   Пользователь A читает баланс 100$.
    *   Пользователь B читает баланс 100$.
    *   A снимает 50$ (пишет 50$).
    *   B снимает 80$ (пишет 20$).
    *   **Итог:** 20$. Но должно быть 0$ (или ошибка, если нельзя уйти в минус). Операция A «пропала».

2.  **Нарушение инвариантов (Business Logic Violation):**
    *   Статус заказа: `Pending`.
    *   Админ A меняет на `Shipped`.
    *   Админ B меняет на `Cancelled`.
    *   Если они сохранились последовательно, итоговый статус зависит от того, кто сохранился **последним**, а не от бизнес-логики (например, нельзя отменить уже отправленный заказ).

3.  **Гонка за ресурсами (Race Condition):**
    *   Последний билет на концерт.
    *   Два запроса проходят проверку `if (stock > 0)`.
    *   Оба делают `UPDATE stock = stock - 1`.
    *   **Итог:** `stock = -1`. Продано два билета на одно место.

4.  **Deadlock (Взаимная блокировка):**
    *   Транзакция A блокирует строку `Order` и пытается обновить `Inventory`.
    *   Транзакция B блокирует `Inventory` и пытается обновить `Order`.
    *   Ждут друг друга. Postgres убьет одну из транзакций с ошибкой `Deadlock detected`.

---

### 2. Способы защиты

#### Способ 1: Оптимистическая блокировка (Optimistic Locking)
**«Сначала сделай, потом проверь, не изменилось ли».**

**Механизм:**
В таблице есть колонка `version` (int).
1.  `SELECT * FROM orders WHERE id=1` -> получаем `version=5`.
2.  Изменяем данные в памяти.
3.  `UPDATE orders SET data=..., version=6 WHERE id=1 AND version=5`.
4.  Если затронуто 0 строк — значит, кто-то другой уже изменил запись. Бросаем исключение.

**Плюсы:**
*   Высокая производительность (нет блокировок при чтении).
*   Идеально для UI: пользователь может редактировать форму долго, а конфликт обнаружится только при сохранении.
*   Легко реализовать в ORM (Doctrine `#[ORM\Version]`).

**Минусы:**
*   При конфликте нужно повторять операцию (Retry) или показывать ошибку.
*   Не подходит, если конфликт происходит очень часто (много ретраев = много нагрузки).

#### Способ 2: Пессимистическая блокировка (Pessimistic Locking)
**«Захвати ресурс сразу, никому другому не позволяй трогать».**

**Механизм:**
`SELECT * FROM orders WHERE id=1 FOR UPDATE`.
Postgres ставит эксклюзивную блокировку на строку. Другие запросы к этой строке будут ждать, пока текущая транзакция не завершится (`COMMIT` или `ROLLBACK`).

**Плюсы:**
*   Гарантированная последовательность.
*   Нет потерянных обновлений.
*   Простая логика «сделал — и оно сохранилось».

**Минусы:**
*   Низкая пропускная способность при высокой нагрузке (все ждут в очереди).
*   Риск Deadlock.
*   Долгая блокировка держит ресурсы в БД.

#### Способ 3: Атомарные SQL-операции (Atomic SQL)
**«Пусть база данных сама посчитает».**

**Механизм:**
Вместо `SELECT` -> `Calc` -> `UPDATE`, делаем один `UPDATE` с расчетом внутри SQL.

```sql
-- Уменьшение остатка на складе
UPDATE products 
SET stock = stock - 1 
WHERE id = 1 AND stock > 0;
```

Если затронуто 0 строк — значит, товара нет. Если 1 — успех.

**Плюсы:**
*   Максимальная атомарность.
*   Не нужно грузить объект целиком в PHP.
*   Идеально для счетчиков (остатки, лайки, просмотры).

**Минусы:**
*   Сложнее вложить сложную бизнес-логику (если нужно проверить 10 правил перед списанием, SQL станет кошмаром).
*   Меньше контроля над транзакцией на стороне приложения.

#### Способ 4: Очереди / Asynchronous Processing
**«Не делай сразу, поставь в очередь».**

**Механизм:**
Запросы на изменение приходят в очередь (RabbitMQ/Kafka/DB). Один воркер (или один поток) обрабатывает их **последовательно** для конкретного ключа (например, `user_id` или `product_id`).

**Плюсы:**
*   Полное устранение гонки (так как обработка последовательная).
*   Равномерная нагрузка.
*   Легко масштабировать.

**Минусы:**
*   Задержка (пользователь не видит изменение сразу).
*   Сложность архитектуры (нужна очередь, идемпотентность).

---

### 3. Сравнение и выбор: Когда какой использовать?

| Сценарий | Рекомендуемый способ | Почему |
| :--- | :--- | :--- |
| **Редактирование профиля** (имя, avatar) | **Optimistic Locking** | Конфликты редки. Пользователь видит форму. Если кто-то уже сохранил — показываем «Данные изменились, перезагрузите». |
| **Списание денег с баланса** | **Pessimistic Locking** или **Atomic SQL** | Конфликты возможны, деньги нельзя терять. Блокировка безопаснее, чем повторная попытка списания. |
| **Покупка билета (последний)** | **Atomic SQL** или **Pessimistic** | Критически важна атомарность `stock > 0`. Atomic SQL проще всего и быстрее. |
| **Высоконагруженный счетчик** (лайки) | **Atomic SQL** | `UPDATE likes SET count = count + 1`. Быстро, без блокировок на чтение. |
| **Сложный бизнес-процесс** (выпуск счета) | **Optimistic Locking** + **Retry** | Если конфликт — переделать. Проще, чем держать блокировку на долгую операцию. |
| **Микросервисная архитектура** | **Event Sourcing** или **Очереди** | Разрешаемые конфликты через события, последовательная обработка воркерами. |

---

### 4. Практический пример в Symfony + PostgreSQL

#### Ситуация: Пользователь меняет пароль.

**Вариант A: Оптимистическая блокировка (Рекомендуемый для CRUD)**

1.  В сущности `User` есть поле `#[ORM\Version] private int $passwordVersion;`.
2.  При сохранении Doctrine автоматически добавляет `WHERE password_version = X` в `UPDATE`.
3.  Если конфликт — ловим `OptimisticLockException` и показываем пользователю: *«Другой пользователь уже изменил данные. Обновите страницу».*

**Вариант B: Пессимистическая блокировка (Для финансовых операций)**

```php
// В репозитории
public function lockAndChangePassword(User $user, string $newPassword): void
{
    $em = $this->getEntityManager();
    
    // 1. Блокируем строку
    $query = $em->createQuery(
        'SELECT u FROM User u WHERE u.id = :id FOR UPDATE'
    );
    $query->setParameter('id', $user->getId());
    $lockedUser = $query->getOneOrNullResult();

    // 2. Логика (проверка старого пароля, хеширование)
    if (!$this->isPasswordValid($lockedUser, $oldPassword)) {
        throw new InvalidPasswordException();
    }
    
    $lockedUser->setPassword($newPassword);
    $em->flush();
    // Транзакция завершится, блокировка снимется
}
```

**Вариант C: Atomic SQL (Для счетчиков)**

```php
// Увеличение прослушиваний
public function incrementViews(Post $post): void
{
    $query = $this->getEntityManager()
        ->createQuery('UPDATE Post p SET p.views = p.views + 1 WHERE p.id = :id');
    
    $result = $query->setParameter('id', $post->getId())->getResult();
    
    if ($result === 0) {
        throw new NotFoundException('Post not found');
    }
}
```

---

### Итог

1.  **По умолчанию** используйте **Optimistic Locking** для большинства бизнес-сущностей. Это современный, быстрый и предсказуемый подход.
2.  Для **денег и ресурсов** (балансы, остатки) используйте **Pessimistic Locking** или **Atomic SQL**, чтобы гарантировать атомарность в момент изменения.
3.  Избегайте `SELECT ... FOR UPDATE` для простых CRUD-операций, если конфликты редки — это снижает производительность.
4.  Всегда обрабатывайте конфликты в коде (Retry или User Feedback), а не оставляйте их как «тихие ошибки».

Выбор зависит от **частоты конфликтов** и **стоимости ошибки**:
*   Редкие конфликты, дорогая ошибка → Пессимистическая блокировка.
*   Частые конфликты, дешевая ошибка (можно повторить) → Оптимистическая блокировка.
*   Простая операция изменения числа → Atomic SQL.

> ⏱ 36.0s · prompt 50745 tok · output 2390 tok

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

**Кандидат:**

Мой Git-процесс — это баланс между **гибкостью разработки** и **надежностью деплоя**. Он построен на принципах GitFlow (улучшенной версии) и Best Practices из крупных проектов.

Вот пошаговый процесс от постановки задачи до продакшена, с объяснением *почему* мы делаем именно так.

---

### 1. Ветвление (Branching Strategy)

Я использую модифицированный **GitFlow**. Это не «традиционный» GitFlow с его жесткостью, а более гибкий подход, часто называемый «Practical GitFlow».

#### Основные ветви:
1.  **`main` (или `master`):**
    *   **Статус:** Всегда готов к релизу. Каждый коммит здесь — это потенциальная релизная версия.
    *   **Правило:** В `main` попадает только через **Pull Request (PR)** с обязательным ревью. Никогда не коммитим напрямую.
    *   **Почему:** `main` — это «источник правды» для продакшена. Если что-то упало, мы знаем, что именно сломалось (последний тег/коммит).

2.  **`develop`:**
    *   **Статус:** Основная ветвь разработки. Здесь скапливаются все фичи и фиксы.
    *   **Правило:** Сливается в `main` только перед релизом. Из `main` не сливается обратно в `develop` (чтобы не тащить дублирующиеся изменения).
    *   **Почему:** Разделение «стабильного» (`main`) и «рабочего» (`develop`) позволяет поддерживать два параллельных потока: текущий релиз и следующую версию.

3.  **Фичи (`feature/...`) и Фиксы (`fix/...`):**
    *   **Статус:** Короткоживущие ветви для конкретной задачи (JIRA/Trello ID).
    *   **Имена:** `feature/123-user-profile-update`, `fix/124-payment-timeout`.
    *   **Почему:** Изоляция задач. Если фича A сломается, она не затронет фичу B. Легко удалить ветку после слияния.

4.  **Релизы (`release/...`):**
    *   **Статус:** Создаются из `develop` когда фичи для версии v1.2 готовы.
    *   **Назначение:** Буфер для исправления багов *только* перед релизом. Новые фичи сюда не добавляются!
    *   **Почему:** Позволяет «заморозить» функциональность и фокусироваться на стабилизации. Если в `release/1.2` найдут баг, чиним его там, а не в `develop` (чтобы не тянуть в `main` недоработанные фичи из `develop`).

5.  **Горячие фиксы (`hotfix/...`):**
    *   **Статус:** Создаются из `main` (где сейчас продакшен).
    *   **Назначение:** Срочные критические баги, которые нельзя ждать до следующего релиза.
    *   **Почему:** Изоляция срочного фикса от основного процесса разработки. После слияния в `main`, фикс также сливается обратно в `develop` (чтобы не потерять его в будущих версиях).

---

### 2. Формат коммитов (Commit Message)

Я использую стандарт **Conventional Commits**. Это не просто «fix bug», а структурированное сообщение.

#### Формат:
```text
<type>(<optional scope>): <subject>

<body>

<footer>
```

#### Примеры:
*   `feat: add user password reset email`
*   `fix(api): handle 429 rate limit response`
*   `refactor: move order validation logic to OrderService`
*   `docs: update API endpoints for v2`
*   `chore(deps): bump symfony/orm from 2.13 to 2.14`

#### Типы (`type`):
*   `feat`: Новая фича.
*   `fix`: Исправление бага.
*   `docs`: Изменение документации.
*   `style`: Форматирование кода (без изменения логики).
*   `refactor`: Рефакторинг (не фича, не фикс).
*   `test`: Добавление тестов.
*   `chore`: Изменение сборки, конфигов, зависимостей.

#### Почему так?
1.  **Автоматизация:** На основе этих типов можно автоматически генерировать **Changelog** (изменения в версии) и **SemVer-версии** (Major/Minor/Patch).
2.  **Читаемость истории:** В `git log` легко фильтровать только `feat` или только `fix`.
3.  **Ревю:** Ревьюер сразу видит, что это за тип изменения.

---

### 3. Pull Request (PR) Процесс

PR — это сердце код-ревью. Он должен содержать максимум информации.

#### Требования к PR:
1.  **Четкий заголовок:** Соответствует типу коммита (Conventional Commits).
2.  **Описание:**
    *   **Что сделано:** Краткое описание.
    *   **Почему:** Бизнес-обоснование или ссылка на задачу.
    *   **Как тестировать:** Шаги для проверки (если нужно вручную).
    *   **Скриншоты/Логи:** Для UI-изменений или сложных фич.
3.  **Маленький размер:**
    *   **Правило:** PR должен содержать **< 500 строк** измененного кода (без автогенерации).
    *   **Почему:** Мозг человека плохо держит контекст больших объемов. Большие PR ревьюят поверхностно, что приводит к багам. Если фича большая — разбиваем на несколько PR.
4.  **Self-Rewview:** Автор сам прогоняет чек-лист перед отправкой:
    *   Код отформатирован (PHP-CS-Fixer/Ruby RuboCop).
    *   Тесты написаны и проходят локально.
    *   Нет `var_dump`/`console.log`.
    *   Нет хардкодов (magic numbers/strings).

---

### 4. Код-ревью (Code Review)

Ревью — это не только поиск багов. Это обмен знаниями.

#### Правила ревью:
1.  **Скорость:** Ревьюер должен ответить в течение **24 часов**.
2.  **Тон:** Конструктивный.
    *   ❌ «Ты сделал неправильно».
    *   ✅ «Я беспокоюсь, что этот метод может упасть, если $id null. Предлагаю добавить проверку».
3.  **Фокус:**
    *   **Логика:** Есть ли баги?
    *   **Читаемость:** Понятен ли код?
    *   **Архитектура:** Соответствует ли принципам SOLID/Clean Architecture?
    *   **Безопасность:** Уязвимости (SQLi, XSS).
    *   **Производительность:** N+1 запросы, тяжелые операции.
4.  **Не обсуждаем стиль:** Стиль проверяет линтер (PHP-CS-Fixer, ESLint). Люди должны спорить о логике, а не о пробелах.

#### Почему важно?
*   **Параноидальный взгляд:** Ревьюер видит то, что автор не видит из-за «близорукости».
*   **Онбординг:** Новые разработчики быстро учатся стандартам проекта.
*   **Дистрибуция знаний:** Если один человек знает часть системы, ревью других заставляет всех знать всю систему.

---

### 5. Тестирование и CI (Continuous Integration)

Перед слиянием в `develop` или `main` CI должен пройти все стадии.

#### Пайплайн CI (напр., GitHub Actions/GitLab CI):
1.  **Static Analysis:**
    *   PHPStan/Psalm (на уровень 8+).
    *   PHP-CS-Fixer (проверка стиля).
    *   *Почему:* Поймать ошибки типов и стиля раньше, чем они попадут в продакшен.
2.  **Unit Tests:**
    *   PHPUnit (юнит-тесты домена и сервисов).
    *   *Почему:* Быстрая проверка логики без зависимости от БД.
3.  **Integration Tests:**
    *   Тесты с реальным PostgreSQL (Docker container).
    *   *Почему:* Проверка маппинга, SQL-запросов, транзакций.
4.  **Build:**
    *   Сборка Docker-образа (если применимо).
    *   *Почему:* Убедиться, что код собирается и зависимости не конфликтуют.

**Правило:** Если CI красный — слияние запрещено (Branch Protection Rules).

---

### 6. Релизы (Releases) и Продакшен

#### Процесс релиза:
1.  **Создание ветки `release/vX.Y.Z`:**
    *   Из `develop`.
    *   Обновляем версию в `composer.json` или `VERSION` файле.
    *   Генерируем `CHANGELOG.md` (автоматически из коммитов `feat/fix`).
2.  **Стабилизация:**
    *   Исправляем только баги.
    *   Проверяем критические сценарии на staging-окружении.
3.  **Слияние в `main`:**
    *   PR из `release/vX.Y.Z` в `main`.
    *   Устанавливаем тег `vX.Y.Z` на этот коммит.
4.  **Слияние обратно в `develop`:**
    *   PR из `release/vX.Y.Z` в `develop`.
    *   *Почему:* Чтобы в `develop` были все фиксы, сделанные в `release`. Иначе они потеряются в следующей версии.
5.  **Деплой:**
    *   Автоматический деплой тега `vX.Y.Z` на продакшен (или вручную, по кнопке).
    *   *Почему:* Мы деплоим **тег**, а не ветку. Тег — это неизменяемый снимок состояния. Если что-то сломалось, мы можем сделать Rollback к предыдущему тегу.

#### Hotfix:
1.  Ветка `hotfix/vX.Y.Z.1` из `main`.
2.  Фикс, тесты.
3.  Слияние в `main` (тег `vX.Y.Z.1`).
4.  Слияние обратно в `develop`.
5.  Деплой.

---

### Почему так, а не иначе?

1.  **Почему не `trunk-based` (одна ветка `main`)?**
    *   Trunk-based хорош для очень маленьких команд и микросервисов. Но для монолита с долгой разработкой фич он создает хаос: одна долгая фича блокирует всю ветку `main`, мешая другим релизам. GitFlow дает изоляцию.

2.  **Почему не `feature flags` вместо веток релизов?**
    *   Feature flags — это отличный инструмент, но он не заменяет ветвление. Флаги усложняют код (проверки `if (flag)`). Ветки `release` позволяют «заморозить» набор фич для конкретной версии, не переплетая их в коде.

3.  **Почему строго Conventional Commits?**
    *   Без стандарта история Git превращается в кашу из «fix», «update», «wip». Автоматическая генерация Changelog и версий эконодит часы ручного труда при каждом релизе.

4.  **Почему маленькие PR?**
    *   Опыт показывает, что вероятность найти баг в ревью снижается экспоненциально с размером PR. Маленькие PR быстрее ревьюятся и легче откатываются.

### Итоговый чек-лист для команды:

1.  Создал задачу в трекере.
2.  Создал ветку `feature/task-id`.
3.  Написал код + тесты.
4.  Прогнал линтеры и статический анализ локально.
5.  Написал коммиты в формате Conventional Commits.
6.  Отправил PR в `develop`.
7.  Провел ревью (2 ревьюера).
8.  CI прошел (зеленый).
9.  Слил в `develop`.
10. Когда накопилось достаточно фич — начали процесс релиза (`release` -> `main` -> тег -> деплой).

Этот процесс предсказуем, безопасен и масштабируется с ростом команды.

> ⏱ 45.2s · prompt 53186 tok · output 2986 tok

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

**Кандидат:**

Для меня хороший код-ревью — это не охота за ошибками, а **совместное улучшение продукта и знаний команды**. Цель — не доказать, что я умнее автора, а убедиться, что код безопасен, читаем и соответствует архитектуре.

Вот мой подход, разделенный на три ключевых аспекта: маркировка, приоритеты и коммуникация.

---

### 1. Маркировка замечаний (Severity Levels)

Чтобы автор понимал, что делать сразу, а что можно обсудить позже, я всегда использую явные теги. В GitHub/GitLab это можно делать через эмодзи или текст в начале комментария.

*   🔴 **[Blocking / Must Fix]** — Блокирующее замечание. Код нельзя слить, пока это не будет исправлено.
    *   *Примеры:* Логическая ошибка, баг, нарушение безопасности, поломка существующей функциональности, отсутствие тестов на критический путь.
*   🟡 **[Suggestion / Should Fix]** — Рекомендация. Желательно исправить до слияния, но если автор согласен с обоснованием — можно оставить как «TODO» или закрыть с оговоркой.
    *   *Примеры:* Нарушение SOLID, сложная структура (слишком много вложенности), возможность упрощения, потенциальные проблемы с производительностью.
*   🟢 **[Nit / Nitpick]** — Мелочь. Не критично. Автор имеет полное право проигнорировать, если он считает, что текущий вариант лучше или понятнее.
    *   *Примеры:* Выбор имени переменной (если текущее не ужасное), предпочтительный синтаксис (например, `match` vs `switch`), небольшие улучшения стиля.
*   ❓ **[Question]** — Вопрос. Я не уверен, правильно ли понял намерение. Здесь нужен диалог, а не указание.
    *   *Пример:* «Зачем здесь `try-catch`? Мы обрабатываем эту ошибку выше в контроллере?»
*   🚀 **[Praise / Nice Work]** — Похвала. Обязательно! Код-ревью не должно быть только критикой.
    *   *Пример:* «Отличная работа с разбивкой этого монстра на маленькие методы. Очень читаемо».

---

### 2. На что смотрю в первую очередь (Приоритеты)

Когда я открываю дифф, я иду по списку проверок от самого важного к менее важному. Если что-то из первого списка плохо — я даже не смотрю на второй.

#### Уровень 1: Корректность и Безопасность (Critical)
1.  **Логика:** Работает ли код так, как описано в задаче? Нет ли граничных случаев (empty arrays, null values, negative numbers)?
2.  **Безопасность:** Есть ли SQL-инъекции, XSS, утечки токенов? Проверены ли права доступа (Auth/Access Control)?
3.  **Данные:** Правильно ли работают транзакции? Нет ли потери данных (Lost Update)? Корректно ли обрабатываются ошибки БД?

#### Уровень 2: Архитектура и Проектирование (High)
4.  **SRP (Single Responsibility):** Класс или метод делают только одну вещь?
5.  **Зависимости:** Нет ли «магических» зависимостей? Всё ли внедрено через конструктор?
6.  **Тестируемость:** Можно ли юнит-тестировать этот код? Если есть жесткая зависимость от `static` методов или глобальных переменных — это плохо.
7.  **Именование:** Названия классов, методов и переменных отражают *интент* (намерение), а не только *тип данных*.
    *   ❌ `$data`, `$value`, `$x`.
    *   ✅ `$userEmail`, `$totalPrice`, `$retryCount`.

#### Уровень 3: Качество и Читаемость (Medium)
8.  **Дублирование (DRY):** Нет ли повторяющегося кода?
9.  **Сложность:** Глубина вложенности. Если вложенность больше 3-4 уровней — нужно рефакторить (early returns, вынесение методов).
10. **Комментарии:**
    *   Хорошие комментарии объясняют *почему* (делали так, а не иначе).
    *   Плохие комментарии дублируют код (`$i++; // увеличиваем i`).
    *   Отсутствуют комментарии там, где сложная бизнес-логика.

#### Уровень 4: Стиль и Инструменты (Low)
11. **Форматирование:** (Если линтер настроен, это его зона ответственности, но иногда люди пропускают).
12. **Статический анализ:** Пройден ли PHPStan/PhpCsFixer?

---

### 3. Как поступаю при разногласии с автором

Разногласия — это нормальная часть процесса. Главное — не превращать ревью в спор о вкусах.

#### Правило 1: Отделяйте факты от мнений
*   **Факт:** «Этот метод нарушает интерфейс `PaymentGateway`, который мы все используем».
*   **Мнение:** «Мне кажется, этот метод слишком долгий».
*   *Действие:* Если есть фактическое нарушение стандартов/архитектуры проекта — я настаиваю (🔴). Если это мнение о стиле — я оставляю выбор за автором (🟢/🟡).

#### Правило 2: Предлагайте альтернативу, а не только критику
*   ❌ «Так писать плохо, потому что медленно».
*   ✅ «Я беспокоюсь, что этот цикл будет медленным при больших массивах. Можно ли заменить его на `array_map` или перенести фильтрацию в SQL-запрос? Вот пример, как это может выглядеть...»
*   *Почему:* Автор может не знать о проблеме. Предложив решение, вы помогаете ему, а не просто указываете на ошибку.

#### Правило 3: «Закрыть с оговоркой» (Close with Context)
Если автор настаивает на своем варианте и у него есть веская причина (например, совместимость со старым модулем, или он знает контекст, который мне не ясен):
*   Я прошу его написать **комментарий в коде**, объясняющий *почему* выбран этот путь.
*   Или мы добавляем тикет в трекер (Jira/Trello) как технический долг.
*   Фраза: «Хорошо, я понял, что здесь важна совместимость. Давайте закроем замечание, но добавь комментарий `// TODO: Refactor to use new API when v3 is out`, чтобы мы не забыли».

#### Правило 4: Эскалация (если спор не конструктивен)
Если мы спорим о дизайне больше 2-3 раундов и не находим решения:
1.  Прошу автора записать свою логику в 3-4 предложениях.
2.  Пишу свою логику.
3.  Предлагаем обсудить голосом (звонком/видеозвонок). 5 минут разговора часто заменяют 30 комментариев.
4.  Если и там не договорились — берем решение у тимлида или стейкхолдера. **Время команды дороже «правильности» одного решения.**

#### Правило 5: Не используйте слово «Всегда» и «Никогда»
*   ❌ «Всегда используй интерфейсы».
*   ✅ «В этом конкретном случае, я бы предпочел интерфейс, потому что нам нужно легко мокать эту зависимость в тестах».

---

### Пример идеального комментария

> **🟡 [Suggestion]**
> Здесь мы создаем новый клиент HTTP в каждом запросе. Это может быть медленным из-за overhead на создание соединения.
>
> *Предложение:* Давайте сделаем его `static` или внедрим через DI-контейнер (как у нас с другим API-клиентом). Тогда мы будем переиспользовать соединение.
>
> *Почему:* Это соответствует нашей текущей архитектуре (Dependency Inversion) и улучшит производительность.
>
> *Если я пропустил контекст, где тут нужна изоляция — скажи, закрою.*

Такой подход показывает, что вы думаете о коде в целом, уважаете опыт автора и предлагаете конструктивные решения.

> ⏱ 30.2s · prompt 56219 tok · output 1935 tok

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

**Кандидат:**

Сырая проблема «пользователи не могут найти заказы» — это описание симптома, а не задачи. Если отдать её разработчику как есть, он может:
1.  Просто добавить поле «Поиск по номеру заказа».
2.  Создать сложную систему фильтрации по датам.
3.  Переработать весь интерфейс личного кабинета.

Все три варианта могут быть верны или неверны, но без контекста мы рискуем потратить недели на то, что не решит боль поддержки.

Вот как я трансформирую этот бизнес-запрос в четкую техническую задачу (User Story + Acceptance Criteria).

---

### Сформулированная задача для исполнителя

**Заголовок:** Реализация фильтрации и сортировки истории заказов в личном кабинете для быстрого поиска конкретного заказа.

**Кто:**
Пользователь, который хочет быстро найти один конкретный старый заказ (например, для возврата или проверки гарантии), но не помнит его номер и дату точную до дня.

**Что делает:**
Система предоставляет удобный интерфейс для поиска заказа по критериям:
1.  **Дата:** Диапазон дат (с ... по ...).
2.  **Статус:** Выборка по статусам (Все, Оплаченные, В пути, Завершенные, Отмененные).
3.  **Ключевые слова (опционально/в будущем):** Поиск по названию товара или номеру заказа (через глобальное поисковое поле или отдельный фильтр).

**Почему (Бизнес-ценность):**
Сокращение потока обращений в поддержку на 20–30% за счет самостоятельного поиска заказов пользователем. Улучшение метрики CSAT (удовлетворенности) по разделу «История заказов».

**Критерии приемки (Acceptance Criteria):**

1.  **UI/UX:**
    *   В списке заказов появляются фильтры: «Дата начала», «Дата окончания», «Статус заказа».
    *   Фильтры сохраняют свое состояние при перезагрузке страницы (query parameters в URL).
    *   Пустое состояние: Если по фильтрам ничего не найдено, показывается понятное сообщение: «Заказов за выбранный период не найдено. Расширьте диапазон дат».

2.  **Функциональность:**
    *   Запрос к БД оптимизирован: фильтрация происходит на уровне SQL (индексы по `created_at` и `status`), а не в PHP по всей истории пользователя.
    *   Максимальный диапазон дат: Не более 12 месяцев назад (чтобы не грести по всей истории с 2019 года).
    *   Сортировка: По умолчанию — новые сверху. Возможность переключить на старые сверху.

3.  **Производительность:**
    *   Время ответа запроса (TTFB) при активированных фильтрах — не более 300 мс.
    *   Проверено на юнит-тесте с датасетом из 1000+ заказов у одного пользователя.

4.  **Тестирование:**
    *   Написаны интеграционные тесты для контроллера: проверка передачи параметров фильтров в сервис.
    *   Написаны юнит-тесты для сервиса/репозитория: проверка корректности DQL/QueryBuilder с разными комбинациями фильтров.

5.  **Аналитика:**
    *   Добавлены события в аналитическую систему (например, Matomo/Amplitude):
        *   `filter_orders_by_date` (с параметром диапазона).
        *   `filter_orders_by_status` (с параметром статуса).
        *   Это поможет бизнесу понять, какой фильтр используют чаще.

---

### Что обязательно включил в формулировку и почему?

#### 1. Контекст и Цель (Why)
*   **Что:** «Сокращение обращений в поддержку».
*   **Почему:** Чтобы разработчик понимал, *почему* это важно. Если он предложит альтернативное решение (например, «Давайте сделаем умный поиск по названию товара, это проще»), аналитик или лид смогут принять решение на основе бизнес-цели, а не личных предпочтений разработчика.

#### 2. Конкретные Критерии (What)
*   **Что:** Я перечислил *конкретные* поля фильтра (Дата, Статус), а не абстрактное «лучший поиск».
*   **Почему:** Убирает туман. Разработчик не должен гадать, что именно искать. Он знает точно, какие данные нужны.

#### 3. Технические ограничения (Constraints)
*   **Что:** «Оптимизированный SQL-запрос», «Максимальный диапазон 12 месяцев».
*   **Почему:** Это предостерегает от «быстрого решения» через выборку всех заказов пользователя и фильтрацию в памяти PHP. Для активных пользователей с тысячами заказов это будет медленно. Указание на SQL и индексы сразу задаст вектор работы репозиторию.

#### 4. Критерии качества (How to verify)
*   **Что:** Время ответа < 300 мс, наличие тестов.
*   **Почему:** Это делает задачу измеримой. «Быстрый поиск» — это субъективно. «300 мс» — объективно. Наличие тестов гарантирует, что функция не сломается при следующем деплое.

#### 5. Метрика успеха (Success Metrics)
*   **Что:** Аналитика по использованию фильтров.
*   **Почему:** После релиза мы сможем посмотреть данные через месяц. Если пользователи используют фильтр «Статус» в 90% случаев, но почти никогда «Дату» — мы поймем, что дата была лишней, и в будущем можем упростить UI. Без аналитики мы никогда не узнаем, сработала ли фича.

### Что я бы уточнил у бизнеса ДО старта (если еще не сделано)?

1.  **Топ-3 причины обращения:** По каким запросам чаще всего пишут в поддержку? «Не вижу заказ», «Хочу вернуть товар», «Хочу поменять адрес»? (Это влияет на то, какие именно действия нужно дать после поиска: кнопка «Вернуть», «Подтвердить получение» и т.д.).
2.  **Глубина истории:** Как далеко в прошлое нужно смотреть? 1 год? 5 лет? (Это влияет на размер данных и производительность).
3.  **Мульти-язычность/валюты:** Если заказы были в разных валютах, нужно ли это отражать в фильтрах? (Скорее нет, но лучше знать).

Такая задача готова к оценке часов и разработке. Она минимизирует риск переделок и гарантирует, что результат будет полезен бизнесу.

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

Чтобы превратить расплывчатую жалобу «не могу найти заказы» в конкретную техническую задачу, я должен провести **Discovery (исследование)**. Мой подход — идти от бизнес-цели к деталям реализации.

Вот список вопросов, сгруппированных по блокам, и *почему* я их задаю.

---

### Блок 1: Анализ проблемы (Почему они не находят?)

*Перед тем как строить новое, нужно понять, почему старое не работает.*

1.  **Какие именно типы запросов чаще всего приходят в поддержку?**
    *   *«Где мой заказ №...?»* (пользователь помнит номер, но не может его ввести).
    *   *«Я заказывал куртку неделю назад, а тут только последние 3 заказа»* (ограничение истории).
    *   *«Я хочу вернуть товар из прошлого месяца»* (нужен не просто поиск, а действия над старым заказом).
    *   **Зачем:** Это определяет, нужен ли просто поиск по дате, или же нужно изменить UX (например, добавить глобальное поле «Поиск по номеру» или «Поиск по товару»).

2.  **Есть ли сейчас какой-либо способ поиска заказов?**
    *   Если да, почему он неудобен? (Сложный интерфейс, медленный, отсутствует).
    *   Если нет, как пользователи сейчас это делают? (Копируют номер из письма, прокручивают бесконечный список).
    *   **Зачем:** Чтобы не изобретать велосипед и не сломать то, что уже работает, если оно работает.

3.  **Какова средняя глубина истории заказов у наших активных пользователей?**
    *   10 заказов? 100? 500?
    *   **Зачем:** Если в среднем 5 заказов, пагинация и фильтры могут быть избыточны. Если 500 — без пагинации и оптимизированного поиска никуда.

---

### Блок 2: Бизнес-правила и ограничения

4.  **Насколько далеко в прошлое мы должны позволять искать заказы?**
    *   Все время? 1 год? 3 года?
    *   **Зачем:** Влияет на производительность БД и UX. Показывать заказы 2015 года может быть нецелесообразно с точки зрения логистики (товары больше нет, гарантия истекла). Также это влияет на юридические требования (хранение данных).

5.  **Что пользователь должен сделать с найденным старым заказом?**
    *   Просто посмотреть статус/состав?
    *   Повторить заказ?
    *   Запросить возврат/обмен?
    *   Понравить адрес (если заказ еще не отправлен)?
    *   **Зачем:** Если цель — возврат, то после поиска должно быть действие «Инициировать возврат». Если цель — просто посмотреть, то достаточно детали заказа. Это меняет архитектуру (наличие кнопок в списке/карточке).

6.  **Есть ли ограничения по правам доступа?**
    *   Может ли менеджер увидеть заказы всех пользователей?
    *   Может ли пользователь увидеть заказы, оформленные на другой email (если один аккаунт связан с несколькими профилями)?
    *   **Зачем:** Чтобы не забыть реализовать роли и права доступа в новом поиске.

---

### Блок 3: Технические детали и интеграции

7.  **Где хранится история заказов?**
    *   В основной БД (PostgreSQL/MySQL)?
    *   Есть ли отдельные сервисы/таблицы для архивных данных?
    *   **Зачем:** Если заказы старше 1 года лежат в холодной хранилище (S3, отдельная таблица), запрос к ним будет медленнее. Нужно ли делать единый интерфейс или показывать только «свежие»?

8.  **Какие поля мы можем использовать для поиска/фильтрации?**
    *   Только дата?
    *   Или также: Имя товара (фuzzy search?), Имя продавца, Способ оплаты, Город доставки?
    *   **Зачем:** Поиск по «Куртка» сложнее, чем по «Дате». Он требует инвертированного индекса (Elasticsearch/Meilisearch) или полнотекстового поиска в БД. Это влияет на стек технологий.

9.  **Какие KPI мы измеряем?**
    *   Снижение обращений в поддержку?
    *   Рост повторных покупок (через функцию «повторить заказ»)?
    *   **Зачем:** Это поможет добавить правильную аналитику (события в Mixpanel/Amplitude) и понять, успешна ли фича.

---

### Блок 4: UX/UI и Приоритеты

10. **Что важнее: скорость разработки или идеальная UX?**
    *   *Вариант A (Быстро):* Простые фильтры (дата, статус) + пагинация.
    *   *Вариант B (Идеально):* Глобальный поиск по товарам, умные подсказки, сохранение истории поиска.
    *   **Зачем:** Помочь заказчику выбрать MVP (Minimum Viable Product). Часто бизнес хочет «всё и сразу», но лучше выпустить простой поиск за неделю, чем идеальный за два месяца.

11. **Есть ли дизайн-макеты?**
    *   Если нет, кто будет их делать?
    *   **Зачем:** Чтобы не переделывать фронтенд 3 раза.

---

### Итоговый чек-лист для постановки задачи

Если я получу ответы, я смогу сформулировать задачу так:

> **Задача:** Улучшить UX поиска заказов в личном кабинете.
> **Цель:** Снизить количество тикетов «Не могу найти заказ» на X% за месяц.
> **Функционал:**
> 1. Добавить фильтр по дате (диапазон) и статусу.
> 2. Добавить глобальное поле поиска по **номеру заказа** (так как это самая частая жалоба).
> 3. Ограничить глубину истории до 2 лет.
> 4. Для старых заказов (старше 30 дней) показывать кнопку «Повторить заказ», но скрывать «Обменять» (так как гарантия истекла).
> **Технически:** Использовать индексы по `user_id`, `created_at`, `status`. Поиск по номеру — через уникальный индекс.
> **Аналитика:** События `filter_applied`, `order_found`, `repeat_order_click`.

Без этих вопросов вы рискуете построить «прекрасный поиск по товарам», а пользователям нужен был просто способ ввести номер заказа из письма.

> ⏱ 24.2s · prompt 58233 tok · output 1545 tok

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

**Кандидат:**

Это две классические ситуации, которые проверяют не только технические навыки, но и **инженерную зрелость** и навыки коммуникации. Разберем каждую по отдельности.

---

### Ситуация А: Нашел попутный баг, не связанный с задачей

**Короткий ответ:** Не чини его «на коленке» в текущем Pull Request. Зафиксируй его и передай в отдельную задачу.

#### Почему?
1.  **Принцип одиночной ответственности (SRP) для PR:** Один PR должен решать одну задачу. Если ты добавишь фикс бага X в PR «Добавить фильтр заказов», ревьюеру будет сложнее понять, что именно изменилось. Если фикс бага X содержит скрытый баг Y, и он сломает продакшен, откатить нужно будет весь PR, включая новый фильтр.
2.  **Регрессия:** Изменение чужого кода (где баг) может непредсказуемо повлиять на твою новую логику.
3.  **Прозрачность для продукта:** Если ты замолчишь баг или чинишь его молча, бизнес не знает, что проблема была. А если ты создашь тикет, команда может решить, критичен ли этот баг для текущего релиза или его можно оставить на потом.

#### Как делаю (Процесс):
1.  **Изоляция:** Убеждаюсь, что баг действительно не зависит от моих изменений (проверяю на `develop` или `main` без моего кода).
2.  **Диагностика:** Кратко описываю, в чем суть бага (шаги воспроизведения, логи, скриншоты).
3.  **Создание тикета:** Создаю задачу в трекере (Jira/Trello) с приоритетом (обычно `Medium` или `High`, если баг критичный, но не блокирующий мою задачу).
    *   *Название:* `Bug: [Краткое описание]. Found while working on [ID моей задачи]`.
4.  **Коммуникация:**
    *   Если баг **критический** (падает приложение, теряются деньги) и мой дедлайн не горит: Предлагаю тимлиду/аналитику приоритетизировать его. Возможно, он встанет в спринт раньше меня.
    *   Если баг **некритический**: Сообщаю: «Коллеги, пока работал над задачей #123, заметил баг #456 (описание). Сделал тикет, предлагаю зачесть его в бэклог».
5.  **Опционально (если дедлайн горит и баг тривиален):**
    *   Если баг чинится одним словом и не затрагивает мою логику, я могу починить его **отдельным коммитом** в том же PR, но **четко пометить** его: `fix: minor typo in error message (unrelated to task #123)`. И **обязательно** сообщить об этом ревьюеру: «Привет, тут есть один лишний коммит с фиксом опечатки, он не связан с фичей, но я заметил и починил. Если не подходит — выкинь».

---

### Ситуация Б: Критерий приемки противоречит цели задачи

**Короткий ответ:** Останавливаешь работу, документируешь противоречие и инициируешь обсуждение с заказчиком (Product Owner) и тимлидом. Не решаешь «за» или «против» самостоятельно.

#### Почему?
1.  **Изменение контракта:** Критерии приемки — это договор между разработчиком и бизнесом. Если ты нарушишь критерий, но сохранишь цель, бизнес может сказать: «Ты сломал требования». Если сохранишь критерий, но потеряешь цель, бизнес скажет: «Решение не работает».
2.  **Технические ограничения:** Возможно, критерий «Строгий ответ за 100 мс» противоречит цели «Полноточный поиск по 10 000 товаров», потому что технически это невозможно без переиндексации, которая займет 3 дня.
3.  **Ответственность:** Разработчик отвечает за *техническую* реализацию, но не за *бизнес-решения*. Выбор между «быстро и грязно» или «правильно и медленно» — за продуктом.

#### Как делаю (Процесс):
1.  **Документирование:**
    *   Формулирую проблему конкретно.
    *   *Пример:* «Критерий №2 требует, чтобы поиск выполнялся по полному тексту названия товара за 50 мс. Цель задачи — дать пользователю быстрый доступ к старым заказам. Однако для 50 мс при глубине истории 5 лет нам нужен Elasticsearch. Если мы используем только Postgres (текущий стек), скорость будет ~500 мс. Противоречие: **Производительность** vs **Стек технологий**».
2.  **Предложение вариантов (A/B/C):**
    *   Никогда не приходи с одним вопросом «Что делать?». Приходи с вариантами решения.
    *   **Вариант 1:** Изменить критерий на «500 мс» и оставить Postgres. (Быстро, просто, но медленнее).
    *   **Вариант 2:** Оставить 50 мс, но ограничить поиск по последним 6 месяцам. (Быстро, но неполный поиск).
    *   **Вариант 3:** Добавить Elasticsearch. (Идеально по скорости, но +3 дня работы и новая зависимость).
3.  **Мой рекомендуемый выбор:**
    *   «Я рекомендую Вариант 2, потому что 80% пользователей ищут недавние заказы. Это решит цель задачи без изменения стека».
4.  **Принятие решения:**
    *   Встреча (или голосовой чат) с PO и Тимлидом.
    *   Обсуждение вариантов.
    *   **Решение фиксируется в тикете**: «Критерий приема №2 изменен на: 500 мс. Причина: технические ограничения текущего стека. Согласовано с [Имя PO]».
5.  **Продолжение работы:**
    *   После фиксации решения ты работаешь уже не противоречащим критерием, а обновленным.

---

### Итоговая таблица действий

| Ситуация | Главное действие | Чего НЕ делать |
| :--- | :--- | :--- |
| **Попутный баг** | Создать тикет, предложить приоритет. | Тихо чинить в своем PR, не сообщив об этом. |
| **Противоречие критерия** | Организовать обсуждение с PO/Лидом с вариантами A/B/C. | Молча выбрать один вариант и сделать его. Или молча бросить работу. |

**Главный принцип:** Разработчик — это эксперт по *возможностям*, а не по *приоритетам*. Ты знаешь, что можно сделать за 1 день, а что за 10. Заказчик знает, что важнее: скорость или качество. Твоя задача — мостик между этими двумя мирами.

> ⏱ 26.6s · prompt 61420 tok · output 1701 tok

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

**Кандидат:**

Мой отчет о выполнении — это не просто констатация факта «я закончил». Это **инструмент передачи контекста** следующему разработчику, QA и Product Owner. Он должен дать полную картину: что сделано, как это работает, что осталось и какие есть риски.

Вот структура моего идеального отчета.

---

### 1. Структура отчета (Когда задача выполнена полностью)

#### А. Резюме (Executive Summary)
Краткое описание результата в 2–3 предложениях.
*   *Пример:* «Реализована фильтрация истории заказов по дате и статусу. Добавлены юнит-тесты и интеграционные проверки. Производительность запросов улучшена на 40% за счет создания составного индекса.»

#### Б. Детальное описание изменений (What & How)
Здесь я перечисляю конкретные технические изменения, чтобы их можно было легко проверить.

1.  **Измененный код:**
    *   Новые классы/методы (ссылки на файлы или названия методов).
    *   Изменения в базе данных (новые таблицы, поля, индексы).
    *   Новые конфигурации или зависимости.
2.  **Архитектурные решения:**
    *   Почему выбрал именно такой подход (например, «Использовал Eager Loading вместо Lazy, чтобы избежать N+1 при отображении списка»).
    *   Если нарушены какие-то общие принципы (например, добавлен технический долг) — честно об этом говорю.
3.  **Тестирование:**
    *   **Покрытие:** Какие сценарии покрыты юнит-тестами?
    *   **Интеграция:** Какие сценарии покрыты интеграционными тестами (с реальной БД)?
    *   **Ручная проверка:** Были ли проведены ручные проверки (если UI изменился, прикладываю скриншоты «До» и «После»).
    *   **Результаты:** Ссылка на отчёт CI/CD (зеленый билд).

#### В. Инструкция по проверке (For QA/PO)
Пошаговая инструкция, как протестировать фичу самостоятельно.
1.  Зайти в личный кабинет.
2.  Перейти в раздел «Мои заказы».
3.  Применить фильтр «Дата: 01.01.2023 – 31.01.2023».
4.  Ожидаемый результат: В списке должны остаться только заказы за этот период.
5.  Очистить фильтры. Ожидаемый результат: Список вернулся к полному.

#### Г. Риски и ограничения (Risks & Limitations)
Даже если всё работает, я всегда указываю границы:
*   «Поиск работает только для заказов за последние 2 года, как и требовалось задачей».
*   «Если пользователь имеет более 10 000 заказов, время отклика может превысить 1 секунду (проверено на нагрузочном тесте)».
*   «В случае изменения структуры таблицы `orders` в будущем, этот индекс может потребовать пересмотра».

#### Д. Ссылки
*   Тикет в трекере.
*   Pull Request.
*   Документация (если обновлял API-доки).

---

### 2. Что меняется, если часть задачи сделать не удалось?

Если задача выполнена частично, отчет должен стать **отчетом о статусе и планом устранения отставания**. Главное правило: **Не скрывать проблему, а четко обозначить её границы.**

#### Изменения в структуре:

1.  **Раздел «Выполнено» vs «Не выполнено»:**
    *   Четко разделяю пункты из Acceptance Criteria.
    *   ✅ *Выполнено:* Фильтрация по дате работает.
    *   ❌ *Не выполнено:* Поиск по названию товара.
    *   ❌ *Не выполнено:* Сохранение истории поиска в localStorage.

2.  **Причина отклонения (Root Cause):**
    *   Почему не получилось?
        *   *Техническая:* «Поиск по названию требует полнотекстового индекса, которого нет в текущей схеме БД, а добавление его потребует миграции и остановки сервиса, что не влезло в дедлайн».
        *   *Бизнес-логическая:* «Выяснилось, что у некоторых пользователей нет права видеть заказы за 2022 год, что противоречит первоначальному ТЗ. Нужна уточненная логика».

3.  **Влияние на пользователя (Impact Analysis):**
    *   Что пользователь *не сможет* сделать?
    *   Есть ли обходной путь (workaround)? (Например: «Пока поиск по товару не работает, пользователь может использовать фильтр по дате»).

4.  **План действий (Next Steps):**
    *   Когда планируется закрыть оставшуюся часть?
    *   Нужны ли дополнительные ресурсы или решения?
    *   Создан ли отдельный тикет для остатка работы?

5.  **Визуализация прогресса:**
    *   Пример: «Из 5 критериев приемки выполнено 3. 2 отложены на следующую итерацию».

---

### Пример фрагмента отчета при частичном выполнении

> **Статус задачи #105: Частично выполнено (80%)**
>
> **Выполнено:**
> 1. ✅ Реализована фильтрация по дате и статусу.
> 2. ✅ Добавлены индексы в БД, время ответа < 200ms.
> 3. ✅ Написаны все юнит-тесты для сервиса.
>
> **Не выполнено (Причины):**
> 4. ❌ **Поиск по названию товара.**
>    *   *Причина:* Требует внедрения Elasticsearch или создания FTS-индекса в Postgres. Оценка времени: +3 дня разработки. Текущий дедлайн не позволяет.
>    *   *Влияние:* Пользователь не может найти заказ по ключевому слову «Куртка». Должен использовать фильтр по дате.
> 5. ❌ **Сохранение фильтров в сессию.**
>    *   *Причина:* Техническая сложность работы с Cookie в текущем фреймворке форм.
>    *   *Влияние:* При перезагрузке страницы фильтры сбрасываются (стандартное поведение).
>
> **Рекомендация:**
> Рекомендую вынести пункты 4 и 5 в отдельную задачу #106 «Улучшение поиска заказов» и планировать ее на следующий спринт. Основная функциональность (фильтр по дате) готова к релизу и закрывает 80% обращений в поддержку.

---

### Почему так?

1.  **Прозрачность:** Заказчик видит реальную картину, а не «мы делаем».
2.  **Управление ожиданиями:** Если часть не сделана, нужно сразу объяснить, чем это компенсируется или когда будет исправлено.
3.  **Безопасность:** Четкое разделение «сделанного» и «остатка» позволяет QA тестировать только то, что должно работать, не тратя время на поиск несуществующих функций.
4.  **Документирование технических долгов:** Если часть работы отложена из-за времени, это становится задокументированным техническим долгом, который можно планировать в будущем.

Такой отчет превращает разработчика из «исполнителя» в «партнера», который управляет процессом и результатами.

> ⏱ 27.4s · prompt 63161 tok · output 1748 tok

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

**Кандидат:**

В PostgreSQL **нет** встроенной инструкции `FORCE INDEX` (в отличие от MySQL или MariaDB).

Если вы встретили в коде или документации упоминание `FORCE INDEX` в контексте PostgreSQL, это, скорее всего, одна из двух ситуаций:
1.  **Перенос мышления из MySQL:** Разработчик привык к синтаксису MySQL и ошибочно полагает, что в Postgres он работает так же.
2.  **Использование расширений или ORM:** Некоторые ORM (например, старые версии Doctrine или специфические библиотеки) могут генерировать псевдо-инструкции, которые Postgres игнорирует или обрабатывает через хинты в `EXPLAIN`.

---

### Как же принудить PostgreSQL использовать нужный индекс?

В PostgreSQL оптимизатор (Planner) выбирает план выполнения запроса на основе **стоимости (cost)**, которую он рассчитывает на основе статистик данных. Если вы хотите «заставить» его использовать конкретный индекс, вы используете следующие методы:

#### 1. `SET enable_* = off` (Самый частый способ)
Это настройки сессии, которые временно отключают определенные стратегии планировщика.

*   **Чтобы заставить использовать `Index Scan` (вместо `Seq Scan`):**
    ```sql
    SET enable_seqscan = off;
    -- Ваш запрос
    SELECT * FROM orders WHERE user_id = 123;
    -- Сбросить после
    SET enable_seqscan = on;
    ```
    *   `SET enable_indexscan = off;` — запрещает использование index scan (если хотите заставить сделать seq scan).
    *   `SET enable_bitmapscan = off;` — и т.д.

*   **Плюсы:** Работает, дает четкий результат.
*   **Минусы:** Это «лопатка». Вы вмешиваетесь в логику оптимизатора. Если статистики устареют, ваш «принудительный» индекс может стать медленным.

#### 2. Покрытие запроса индексом (Covering Index)
Иногда оптимизатор выбирает `Seq Scan`, потому что индекс не покрывает все нужные поля, и ему приходится делать дополнительные обращения к таблице (heap fetch).

*   **Решение:** Создать индекс с `INCLUDE` (в Postgres 11+).
    ```sql
    -- Обычный индекс
    CREATE INDEX idx_orders_user ON orders (user_id);

    -- Покрытие (Covering) индекс
    CREATE INDEX idx_orders_user_covering 
    ON orders (user_id) 
    INCLUDE (status, total_amount);
    ```
    Теперь оптимизатор видит, что может получить все данные *только из индекса* (Index Only Scan), что часто делает его дешевле, чем Seq Scan + Heap Fetch.

#### 3. Обновление статистик (`ANALYZE`)
Часто оптимизатор «ошибается», потому что его статистики устарели (например, вы недавно вставили миллион строк, а статистики говорят, что их 1000).

*   **Решение:**
    ```sql
    ANALYZE orders;
    ```
    После этого оптимизатор пересчитает стоимость планов и, вероятно, выберет правильный индекс сам.

#### 4. Использование `EXPLAIN` для диагностики
Прежде чем применять «силовые методы», всегда смотрите, почему оптимизатор выбрал текущий план.
```sql
EXPLAIN (ANALYZE, BUFFERS) SELECT ...;
```
Ищите в плане:
*   `Index Scan` / `Index Only Scan` — хорошо.
*   `Seq Scan` — плохо (если таблица большая).
*   `Rows Removed by Filter` — плохой индекс.

---

### В каких случаях нужно принудительное использование индекса?

1.  **Оптимизатор «сходит с ума»:**
    Вы уверены, что `Index Scan` будет быстрее, но Postgres выбирает `Seq Scan`. Это бывает, если:
    *   Данные распределены неравномерно (data skew).
    *   Статистики неверны.
    *   Размер таблицы небольшой, и `Seq Scan` объективно быстрее (но вы хотите предсказуемость).

2.  **Тестирование гипотез:**
    Вы хотите проверить, какой из двух индексов лучше подходит для конкретного сценария. Включаете/выключаете `enable_seqscan` и сравниваете время в `EXPLAIN ANALYZE`.

3.  **Временное решение при недоступности `ANALYZE`:**
    Если вы не можете выполнить `ANALYZE` (например, таблица слишком большая и анализ займет часы), а запрос тормозит, `SET enable_seqscan = off` может дать мгновенный эффект.

---

### Важное предупреждение

Использование `SET enable_* = off` в продакшене — **крайняя мера**.
*   Это локально для сессии/транзакции.
*   Оно не масштабируется.
*   Лучше найти корень проблемы (неправильный индекс, устаревшие статистики, неправильная структура запроса).

**Итог:** В PostgreSQL нет `FORCE INDEX`. Вместо этого вы управляете поведением оптимизатора через настройки сессии (`enable_seqscan`, `enable_indexscan`) и улучшение качества индексов (`ANALYZE`, `Covering Indexes`).

> ⏱ 19.8s · prompt 64934 tok · output 1226 tok

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

**Кандидат:**

Если отвечать коротко: **на 0%.**

В подавляющем большинстве случаев включение `declare(strict_types=1)` **не влияет на производительность** выполнения кода (runtime).

А теперь подробнее, почему это так и откуда взялось заблуждение о замедлении.

### 1. Почему нет замедления?

*   **Проверка происходит на этапе компиляции/сборки, а не в цикле:**
    Когда PHP загружает класс или функцию, он строит таблицу символов и проверяет сигнатуры методов. Если в файле объявлено `strict_types=1`, PHP запоминает этот флаг для функций, определенных *в этом файле*.
    При вызове функции интерпретатор проверяет тип аргумента. Эта проверка (сопоставление типа переданного значения с объявленным) происходит **всегда**, независимо от того, включен ли `strict_types` или нет.
    *   Если `strict_types=0`: PHP пытается «привести» (cast) значение к нужному типу. Если привести не получается — бросает `TypeError`.
    *   Если `strict_types=1`: PHP сравнивает типы. Если они не совпадают ровно-в-ровно — бросает `TypeError`.
    *   **Разница:** В первом случае PHP может делать немного лишней работы (попытка каста), но в современном PHP 8+ эти операции оптимизированы настолько хорошо, что разница в наносекундах на операцию незаметна в реальных приложениях (где доминируют I/O, сетевые запросы и работа с БД).

*   **Нет дополнительных вызовов:** `strict_types` не добавляет никаких новых функций, циклов или проверок внутри тела метода. Это просто модификатор поведения механизма типизации, который и так уже есть в движке.

### 2. Откуда взялся миф о замедлении?

1.  **Путаница со статическими анализаторами:**
    Разработчики часто путают `strict_types` (PHP) с работой инструментов вроде **PHPStan** или **Psalm**. *Они-то* действительно могут работать медленно, потому что анализируют весь код проекта, строят граф типов и проверяют сотни комбинаций. Но это происходит в CLI при запуске анализатора, а не в web-сервере во время обработки запроса.

2.  **Исторический контекст (PHP 5/ранний PHP 7):**
    В очень старых версиях PHP проверка типов была менее оптимизирована. Но еще до того, как `strict_types` стал массовым (PHP 7.0+), а тем более в PHP 8, движок Zend Engine был сильно переработан.

3.  **Психологический эффект:**
    Когда вы видите `TypeError` вместо тихого `Warning` или автоматического приведения, вам *кажется*, что система «думает» дольше, потому что она «строже». Но это просто другое поведение ошибки.

### 3. Единственный случай, где может быть разница (минимальная)

Если ваш код делает **миллионы** вызовов функций, передавая аргументы неправильного типа, которые при `strict_types=0` успешно приводятся (например, строка `"123"` передается в `int` параметр), то:
*   При `strict_types=0`: PHP делает cast.
*   При `strict_types=1`: PHP сразу бросает исключение (если тип не совпал) или сравнивает (если совпал).

Но если тип совпал, разница в проверке «ровно ли» vs «можно ли привести» пренебрежимо мала. А если тип не совпал, то при `strict_types=1` вы получаете ошибку сразу, а не логический баг позже, который будет стоить вам часов отладки.

### Вывод

*   **Производительность:** `strict_types=1` практически бесплатен.
*   **Надежность:** Он бесплатен только в том смысле, что *вы* не платите за него ресурсами CPU. Вы платите за него вниманием при написании кода (нужно точно указывать типы).
*   **Рекомендация:** Всегда включайте `declare(strict_types=1)` во всех своих файлах. Это один из самых сильных инструментов для предотвращения багов, и его «цена» в производительности равна нулю.

> ⏱ 15.9s · prompt 66194 tok · output 985 tok

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

**Кандидат:**

Это распространяющееся заблуждение: **Doctrine ORM *поддерживает* составные первичные ключи (Composite Primary Keys)**. Однако поддержка эта «неудобная» и имеет серьезные ограничения, из-за чего в индустрии их尽量避免 (избегают) в пользу суррогатных ключей (surrogate keys, например, `id BIGINT AUTO_INCREMENT`).

Давайте разберем, что именно поддерживает, что нет, и как это обходится.

---

### 1. Что поддерживает Doctrine?

Вы можете определить составной ключ через атрибуты или XML/Annotations:

```php
#[ORM\Entity]
class Subscription
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    private int $userId;

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    private int $planId;

    // ...
}
```

В этом случае:
1.  `find($id)` примет массив: `$em->find(Subscription::class, ['userId' => 1, 'planId' => 5])`.
2.  Связи `ManyToOne` могут ссылаться на такой составной ключ (если вы правильно настроите `inversedBy` и `joinColumns`).

### 2. Почему его «не любят» и избегают? (Oграничения)

#### A. Связи (Relationships) — больное место
Если у вас есть другая сущность `Order`, которая ссылается на `Subscription` через `ManyToOne`, а `Subscription` имеет составной ключ, то:
1.  В таблице `orders` будут лежать **два** внешних ключа: `subscription_user_id` и `subscription_plan_id`.
2.  При загрузке данных Doctrine должна собирать эти два поля вместе, чтобы идентифицировать родительский объект.
3.  **Ленивая загрузка (Lazy Loading) работает хуже:** Если вы достанете `Order`, а потом захотите обратиться к `$order->subscription->getDetails()`, Doctrine должна выполнить отдельный запрос, используя *оба* поля. Это усложняет SQL и увеличивает риск ошибок маппинга.
4.  **Идентичность в Unit of Work (UoW):** UoW отслеживает сущности по их ID. Для составного ключа он строит хэш из всех частей. Если вы измените одну часть ключа после сохранения, вы можете получить непредсказуемое поведение (UoW может потерять связь с объектом).

#### B. Нет поддержки `EM::persist` для вложенных составных ключей в некоторых сложных сценариях
Если составной ключ содержит ссылок на другие сущности (что бывает редко, но бывает), маппинг становится кошмаром.

#### C. Тестирование и Mocking
В юнит-тестах проще мокать сущность с `int $id`, чем с массивом `['a' => 1, 'b' => 2]`.

#### D. Миграции и Бэкапы
При восстановлении базы данных или создании бэкапов, составные ключи иногда требуют более внимательного порядка вставки данных (сначала `users`, потом `plans`, потом `subscriptions`).

---

### 3. Как с этим живут в реальных проектах?

#### Вариант 1: Суррогатный ключ (Наиболее частый подход)
Даже если логический бизнес-ключ составной (например, `user_id` + `product_id` в корзине), мы добавляем автоинкрементный `id`.

```php
#[ORM\Entity]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null; // Суррогатный ключ

    #[ORM\Column(type: Types::INTEGER)]
    private int $userId;

    #[ORM\Column(type: Types::INTEGER)]
    private int $productId;

    #[ORM\UniqueConstraint(columns: ['userId', 'productId'])] // Логическая уникальность
}
```
**Плюсы:**
*   Простые ссылки (`order_item_id` в других таблицах).
*   Быстрая загрузка.
*   Простые тесты.
*   БД все равно гарантирует уникальность бизнес-пары через `UNIQUE INDEX`.

**Минусы:**
*   В таблице `cart_items` на 1 колонку больше.
*   При обновлении `userId` или `productId` нужно быть осторожным (сначала удалить старую запись, вставить новую, или обновить и полагаться на уникальный индекс).

#### Вариант 2: Таблица-связь (Many-to-Many)
Если вам нужно связать два сущности, где отношение «многие ко многим», Doctrine обычно генерирует промежуточную таблицу (join table).
*   Если у промежуточной таблицы нет доп. полей (weight, status) — используется стандартный M2M.
*   Если есть доп. поля — используется промежуточная Entity (как в Варианте 1 выше).

#### Вариант 3: Нормализация
Если составной ключ возникает из-за того, что вы храните данные, которые должны быть в отдельных сущностях, лучше нормализовать.
*   *Плохо:* Таблица `payments` с ключом `(order_id, method)`.
*   *Хорошо:* Таблица `orders` (ID), таблица `payments` (ID, order_id, method).

---

### 4. Когда все-таки стоит использовать составной ключ в Doctrine?

1.  **Таблицы-связи без доп. полей:** (Например, связь «Роль» и «Пользователь», если нет доп. атрибутов у связи).
2.  **Легаси-системы:** Если вы пишете адаптер для старой БД, где составные ключи уже есть, и менять схему нельзя.
3.  **Аналитические таблицы (Data Warehouse):** Где запросы всегда идут по составному ключу, и запись данных редка.

---

### 5. Подводные камни при использовании составных ключей

1.  **`Repository::find()`** требует массив:
    ```php
    $subscription = $repo->find(['userId' => 1, 'planId' => 5]);
    ```
    Легко ошибиться в порядке или именах ключей.

2.  **Сравнение объектов:**
    Два объекта `Subscription` с одинаковыми `userId` и `planId` считаются **одинаковыми**. Если вы измените `planId` у одного из них после загрузки, UoW может запутаться.

3.  **Исключения:**
    `EntityNotFoundException` будет содержать оба значения, что может упростить отладку, но также может усложнить обработку ошибок в коде (нужно проверять оба условия).

### Итог

**Doctrine поддерживает составные ключи, но они неудобны.**

В 90% случаев в современных PHP-приложениях (Symfony/Laravel) используют **суррогатный автоинкрементный ID** + **уникальный индекс** на бизнес-поля. Это дает лучшую производительность, простоту маппинга и совместимость с ORM-инструментами.

> ⏱ 25.7s · prompt 67213 tok · output 1628 tok

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

Этот код — классический пример **«спагетти-кода»**, который часто встречается в legacy-проектах или у начинающих разработчиков. В нем собраны почти все антипаттерны, которые мы обсуждали ранее.

Давайте разберем проблемы по приоритету (от самых критичных к косметическим) и затем переделаем код.

### 1. Критические проблемы (Баги и Безопасность)

1.  **SQL-инъекция (SQL Injection):**
    ```php
    $id = $req->get('order_id');
    // ...
    "SELECT * FROM orders WHERE id = $id"
    ```
    Переменная `$id` вставляется прямо в строку SQL без экранирования. Злоумышленник может передать `1; DROP TABLE users; --` или что-то худшее.
    *   **Решение:** Использовать предподготовленные выражения (Prepared Statements) или ORM.

2.  **Отсутствие транзакции (Race Condition / Data Consistency):**
    Между `SELECT` и `UPDATE` состояние заказа может измениться (например, другой процесс уже оплатил его, или пользователь отменил заказ).
    *   Вы читаете статус `pending`, но обновляете на `paid`, даже если статус уже `shipped` или `cancelled`.
    *   **Решение:** Логика оплаты должна быть атомарной и проверять текущее состояние перед изменением.

3.  **Жесткая связь с инфраструктурой (Глобальные вызовы):**
    `DB::connect()`, `Mailer::send()`, `new Logger()`.
    *   Вы не можете юнит-тестировать этот контроллер без реальной БД и почтового сервера.
    *   Вы не можете легко заменить почтовый провайдер (с SendGrid на Mailgun) без изменения кода контроллера.
    *   **Решение:** Внедрение зависимостей (Dependency Injection).

4.  **Нарушение SRP (Single Responsibility Principle):**
    Контроллер делает всё: парсит запрос, ходит в БД, меняет бизнес-статус, шлет почту, пишет лог.
    *   **Решение:** Контроллер должен только принимать запрос и возвращать ответ. Бизнес-логика — в сервисе/домене.

5.  **Выборка `SELECT *`:**
    Вы читаете все поля заказа, хотя для отправки письма нужен только `email`.
    *   **Решение:** Выбирать только нужные поля или полагаться на маппинг ORM.

---

### 2. Как бы я это переделал? (Пошагово)

Мой подход — **Чистая Архитектура** (упрощенная версия для Symfony/стандартного PHP).

#### Шаг 1: Доменная модель (Domain)
Сначала я определяю, что такое «Заказ» и каковы правила его оплаты. Это чистый PHP, без БД.

```php
// Domain/Order.php
final readonly class Order
{
    public function __construct(
        private int $id,
        private string $email,
        private OrderStatus $status
    ) {}

    public function canBePaid(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    public function markAsPaid(): self
    {
        if (!$this->canBePaid()) {
            throw new DomainException('Order is not in payable state');
        }
        // Так как класс readonly, мы создаем новый экземпляр (Immutability)
        return new self($this->id, $this->email, OrderStatus::Paid);
    }
    
    // Getters...
}

enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Shipped = 'shipped';
}
```

#### Шаг 2: Реализация хранения (Infrastructure/Repository)
Здесь мы работаем с БД. Мы не храним «сырые» данные, а возвращаем объекты Домена.

```php
// Domain/OrderRepositoryInterface.php (Порт)
interface OrderRepositoryInterface
{
    public function findById(int $id): ?Order;
    public function save(Order $order): void;
}

// Infrastructure/Doctrine/OrderRepository.php (Адаптер)
class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function findById(int $id): ?Order
    {
        $entity = $this->em->find(OrderEntity::class, $id);
        if (!$entity) return null;
        
        return new Order(
            $entity->getId(),
            $entity->getEmail(),
            OrderStatus::from($entity->getStatus())
        );
    }

    public function save(Order $order): void
    {
        $entity = $this->em->find(OrderEntity::class, $order->getId());
        if (!$entity) throw new Exception('Order not found for saving');
        
        $entity->setStatus($order->getStatus()->value);
        $this->em->flush();
    }
}
```

#### Шаг 3: Бизнес-логика (Application Service)
Здесь оркестрируется процесс: загрузка, проверка, изменение, сохранение, отправка почты.

```php
// Application/PayOrderService.php
class PayOrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepo,
        private EmailSenderInterface $emailSender // Интерфейс, а не конкретный Mailer
    ) {}

    public function pay(int $orderId): void
    {
        $order = $this->orderRepo->findById($orderId);
        
        if (!$order) {
            throw new OrderNotFoundException();
        }

        // Доменная логика: можно ли оплатить?
        $updatedOrder = $order->markAsPaid(); 

        // Сохраняем новое состояние
        $this->orderRepo->save($updatedOrder);

        // Побочный эффект: отправляем письмо
        // Это делается ПОСЛЕ успешного сохранения, чтобы не слать письма на несуществующие изменения
        $this->emailSender->send($updatedOrder->getEmail(), 'Ваш заказ оплачен');
        
        // Логирование (можно через Event Dispatcher или логгер)
    }
}
```

#### Шаг 4: Контроллер (Interface Layer)
Теперь контроллер стал тонким и чистым.

```php
// Controller/OrderController.php
class OrderController
{
    public function __construct(private PayOrderService $payOrderService) {}

    #[Route('/orders/{id}/pay', methods: ['POST'])]
    public function pay(int $id): Response
    {
        try {
            $this->payOrderService->pay($id);
            return new Response('ok', 200);
        } catch (OrderNotFoundException $e) {
            return new Response('not found', 404);
        } catch (DomainException $e) {
            // Например, заказ уже оплачен или отменен
            return new Response($e->getMessage(), 409); // Conflict
        } catch (\Throwable $e) {
            // Логирование внутренней ошибки
            return new Response('internal error', 500);
        }
    }
}
```

#### Шаг 5: Интерфейс для почты (для тестирования)

```php
// Domain/EmailSenderInterface.php
interface EmailSenderInterface
{
    public function send(string $to, string $subject): void;
}

// Infrastructure/Mailer/SmtpEmailSender.php
class SmtpEmailSender implements EmailSenderInterface
{
    public function send(string $to, string $subject): void
    {
        // Реальная логика отправки через SwiftMailer/Symfony Mailer
    }
}
```

---

### С чего начать рефакторинг и почему?

Если бы я пришел в этот проект с этим кодом, я бы действовал в такой последовательности:

1.  **Исправить SQL-инъекцию (Критично).**
    *   *Почему:* Это дыра в безопасности. Даже если код уродлив, данные должны быть безопасны.
    *   *Как:* Заменить строковый запрос на Prepared Statement или включить ORM. Это можно сделать за 5 минут, не ломая архитектуру.

2.  **Вынести бизнес-логику из контроллера (SRP).**
    *   *Почему:* Сейчас логику «как оплатить заказ» нельзя переиспользовать (например, из CLI-скрипта или из другого endpoint).
    *   *Как:* Создать класс `OrderService`, перенести туда код между `$pdo->query` и `return`.

3.  **Внедрить зависимости (DI).**
    *   *Почему:* Чтобы код стал тестируемым.
    *   *Как:* Убрать `DB::connect()` и `new Mailer()` из кода. Передавать их через конструктор.

4.  **Добавить тесты.**
    *   *Почему:* Чтобы убедиться, что рефакторинг не сломал логику.
    *   *Как:* Сначала написать тесты для старого кода (Characterization Tests), затем рефакторить, пока тесты не зеленые.

5.  **Ввести Домен (DDD).**
    *   *Почему:* Чтобы код стал понятным бизнесу и устойчивым к изменениям.
    *   *Как:* Создать классы `Order` и `OrderStatus`, вынести правила валидации в них.

### Итог

Исходный код работает «здесь и сейчас», но он **хрупкий, небезопасный и неподдерживаемый**.
Переделанный код чуть сложнее в структуре (больше файлов), но он **безопасен (prepared statements), тестируем (DI), масштабируем (интерфейсы) и понятен (чистый домен)**.

В долгосроке второй вариант сэкономит команде месяцы отладки и переделок.

> ⏱ 36.2s · prompt 69049 tok · output 2285 tok

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

Этот класс — отличный пример того, что часто называют «антисущностью» (Anti-Entity) или «антимоделью». Здесь нарушены сразу несколько фундаментальных принципов DDD и чистой архитектуры.

Давайте разберем ошибки по пунктам, от самых критичных к менее очевидным, и посмотрим, как это исправить.

### 1. Главная проблема: Нарушение инкапсуляции и ответственности (SRP)

**Проблема:**
Метод `changeEmail()` делает две вещи:
1.  Меняет состояние домена (`$this->email = ...`).
2.  Сохраняет это состояние в базу данных (`$this->em->flush()`).

**Почему это плохо:**
*   **Домен не знает о инфраструктуре:** Класс `User` (доменная сущность) не должен знать, что такое `EntityManagerInterface`. Он должен просто менять свое состояние. Сохранение — это техническая деталь.
*   **Трудности с тестированием:** Чтобы протестировать смену email, вам нужен мок `EntityManagerInterface` или реальная БД. Но вы просто хотите проверить, что `$user->changeEmail('new@example.com')` изменило поле email. Этого сейчас сделать чистым образом нельзя.
*   **Проблема транзакций:** Если вы измените несколько полей пользователя (`changeEmail`, `changeName`) и вызовете `flush()` после каждого, вы совершите 2 транзакции. Если после первого изменения упадет второе, вы получите несогласованное состояние (email изменен, имя старое). `flush()` должен вызываться один раз на всю операцию (Use Case).

### 2. Вторичная проблема: «Жирный» конструктор

**Проблема:**
`private EntityManagerInterface $em` передается в конструктор.

**Почему это плохо:**
*   Сущность `User` становится зависимой от ORM. Если вы завтра перейдете с Doctrine на Eloquent или другую БД, этот код сломается.
*   `EntityManager` обычно является Singleton'ом или сервисом приложения, а не частью данных пользователя.

### 3. Терминалогия (Ubiquitous Language)

**Проблема:**
Метод называется `email()`.

**Почему это плохо:**
В PHP (и большинства языках) методы-геттеры обычно начинаются с `get` или `is`. Хотя есть аргументы в пользу «глагольных» геттеров (functional style), в контексте DDD лучше использовать понятные имена. `email()` выглядит как действие, а не как доступ к данным. Но это мелочь по сравнению с двумя предыдущими пунктами.

---

### Как исправить? (Чистая архитектура)

Мы разделим ответственности:
1.  **Домен (`User`):** Только состояние и бизнес-правила.
2.  **Приложение/Инфраструктура:** Сохранение и работа с БД.

#### Шаг 1: Чистая доменная сущность

```php
namespace App\Domain;

// Обратите внимание: нет use Doctrine\...
class User
{
    private string $email;
    
    // Допустим, у нас есть ID
    private ?int $id = null; 

    public function __construct(string $email)
    {
        $this->validateEmail($email);
        $this->email = $email;
    }

    /**
     * Бизнес-правило: можно ли изменить email?
     * Например, нельзя менять email, если пользователь уже имеет верифицированные уведомления.
     */
    public function changeEmail(string $newEmail): void
    {
        $this->validateEmail($newEmail);
        
        // Только изменение состояния. Никаких SQL!
        $this->email = $newEmail;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    private function validateEmail(string $email): void
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('Invalid email format');
        }
    }
}
```

*В таком виде класс `User` можно легко юнит-тестировать: создать объект, вызвать `changeEmail`, проверить результат. БД не нужна.*

#### Шаг 2: Реализация хранения (Repository в Infrastructure)

Теперь логика сохранения выносится в репозиторий или сервис.

```php
namespace App\Infrastructure\Persistence;

use App\Domain\User;
use Doctrine\ORM\EntityManagerInterface;

class UserPersistenceService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function save(User $user): void
    {
        // Находим сущность в Doctrine (или создаем новую)
        // Здесь происходит маппинг Domain Object -> Doctrine Entity
        // (В реальном проекте вы бы использовали отдельные классы UserEntity и User, 
        // и маппер между ними, но для примера оставим упрощенно)
        
        // ... логика persist ...
        
        // Flush вызывается здесь, один раз для всей операции
        $this->em->flush();
    }
}
```

#### Шаг 3: Use Case (Application Layer)

Здесь происходит оркестрация: загрузили, изменили, сохранили.

```php
namespace App\Application;

use App\Domain\User;
use App\Infrastructure\Persistence\UserPersistenceService;
use App\Domain\UserRepositoryInterface; // Интерфейс из домена

class ChangeUserEmailUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private UserPersistenceService $userSaver
    ) {
    }

    public function execute(int $userId, string $newEmail): void
    {
        // 1. Загружаем агрегат (доменный объект)
        $user = $this->userRepo->findById($userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        // 2. Применяем бизнес-логику (изменение состояния)
        $user->changeEmail($newEmail);

        // 3. Сохраняем результат (инфраструктурная операция)
        $this->userSaver->save($user);
        
        // Здесь же можно отправить событие UserEmailChangedEvent
    }
}
```

### Итог изменений

| Аспект | Было (Плохо) | Стало (Хорошо) |
| :--- | :--- | :--- |
| **Зависимости** | `User` зависит от `EntityManager` | `User` не зависит от ORM. `UserSaver` зависит от ORM. |
| **Тестируемость** | Нужна БД для теста смены email | Чистый юнит-тест без БД. |
| **Атомарность** | `flush()` внутри метода (риск частичного сохранения) | `flush()` в конце Use Case (полное сохранение). |
| **Гибкость** | Сложно заменить БД | Легко заменить реализацию `UserPersistenceService`. |

**Главный урок:** **Доменные сущности описывают *что* происходит, а инфраструктура описывает *как* это сохраняется.** Никогда не смешивайте эти два мира внутри одного метода.

> ⏱ 27.2s · prompt 71480 tok · output 1664 tok

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

**Кандидат:**

Решение технической проблемы на неизвестном стеке — это не тест на эрудицию, а тест на **алгоритмическое мышление** и **управление рисками**. Я не пытаюсь угадать ответ, я системно сужаю пространство поиска.

Вот моя последовательность действий, разбитая на 5 фаз, и критерии, когда я зову на помощь.

---

### Фаза 1: Разведка (Context Gathering) — 15–30 минут
*Цель: Понять контекст, а не код. Не писать ни строчки.*

1.  **Сформулировать проблему в терминах бизнеса/пользователя:**
    *   «Что именно сломано?»
    *   «Что происходило раньше, и что изменилось?»
    *   «Есть ли воспроизводимые шаги?»
2.  **Найти «свидетелей»:**
    *   Лог-файлы (стек-трейсы — это золото).
    *   Изменения в конфигурации (Docker, K8s, Env-переменные).
    *   Последние коммиты, которые затронули эту область.
3.  **Определить границы системы:**
    *   Где начинается эта технология, а где заканчивается? (Например, это ошибка в Rust-сервисе, или в PHP-клиенте, который к нему ходит?)
    *   Есть ли официальная документация или примеры (Examples) в репозитории?

**Критерий перехода к Фазе 2:** Я знаю, *где* искать (в логах, в коде конкретной функции, в конфиге) и *какие* симптомы наблюдаю.

---

### Фаза 2: Минимальная воспроизведимость (Reproduction) — 1–4 часа
*Цель: Получить стабильный способ увидеть ошибку.*

1.  **Минимизация:**
    *   Убираю лишнее. Если ошибка воспроизводится в большом микросервисе, пытаюсь воспроизвести её в минимальном скрипте или изолированном модуле.
2.  **Бисекция (Binary Search):**
    *   Если ошибка появилась после обновления зависимости: откатываю версии пополам.
    *   Если ошибка в новом коде: откатываю коммиты.
3.  **Локальная изоляция:**
    *   Создаю Docker-контейнер с точной копией продакшен-окружения (версии ОС, библиотек).
    *   *Важно:* Часто проблема в несовпадении версий (например, PHP 8.1 в dev и 8.3 в prod).

**Критерий перехода к Фазе 3:** Я могу стабильно вызвать ошибку одной командой или скриптом.

---

### Фаза 3: Гипотезы и эксперименты — 2–8 часов
*Цель: Находить корневую причину (Root Cause).*

1.  **Формулирование гипотез:**
    *   «Может быть, это из-за конкурентности?»
    *   «Может быть, это несовместимость версий?»
    *   «Может быть, это ошибка в маппинге данных?»
2.  **Эксперименты (Scientific Method):**
    *   Меняю ОДНОГО параметра за раз.
    *   Читаю исходный код библиотеки (Source Code Reading). Не документации, а именно кода. Если я не понимаю, как работает функция, я читаю её реализацию.
    *   Гуглю ошибку с точным стеком-трейсом (Stack Trace) + версию библиотеки.
3.  **Запросы к «экспертам» (внутренним или внешним):**
    *   Если технология новая: читаю GitHub Issues.
    *   Если технология проприетарная: пишу тикет саппорту или спрашиваю архитектора.

**Критерий перехода к Фазе 4:** Я понимаю *механизм* ошибки (почему это происходит), даже если еще не придумал *решение*.

---

### Фаза 4: Решение и валидация — 2–6 часов
*Цель: Исправить и проверить.*

1.  **Выбор решения:**
    *   *Workaround:* Быстрый костыль (например, обновить версию, добавить retry).
    *   *Root Fix:* Правильное исправление (починить логику, изменить архитектуру).
    *   *Upgrade:* Обновить стек.
2.  **Реализация:**
    *   Пишу минимальный фикс.
    *   Написываю тест, который падал бы без фикса (Regress Test).
3.  **Валидация:**
    *   Запускаю тесты.
    *   Проверяю на Staging-окружении.
    *   Смотрю на метрики (CPU, Memory, Latency) после деплоя.

---

### Фаза 5: Закрытие и рефлексия
*Цель: Извлечь знание.*

1.  **Документирование:**
    *   Пишу Post-Mortem или заметку в Wiki: «Что случилось, как чинили, как избежать в будущем».
2.  **Обратная связь:**
    *   Рассказываю команде, что было трудно и что я узнал.

---

### Когда просить помощь у команды, а когда разбираться самому?

Это ключевой вопрос инженерной зрелости.

#### 🟢 Разбираюсь САМОСТОЯТЕЛЬНО, если:
1.  **Это вопрос «как это работает», а не «почему оно сломано».**
    *   *Пример:* Я не знаю, как настроить новый логгер в Symfony. Я могу почитать доки и примеры. Это рутинное обучение.
2.  **Я уже проделал Фазы 1 и 2.**
    *   У меня есть воспроизводимый кейс, логи и понимание контекста. Я могу дать конкретный вопрос коллеге, а не «у меня ничего не работает».
3.  **Это вопрос синтаксиса или конфига.**
    *   *Пример:* Как написать правильный YAML для K8s? Это поисковик + документация.
4.  **Проблема не критическая (P2/P3).**
    *   Есть время на эксперименты.

#### 🔴 Зову ПОМОЩЬ (Команду/Архитектора/Саппорт), если:
1.  **Проблема критическая (P0/P1) и блокирует других.**
    *   Если продакшен лежит, а я копюю логи по 2-ю часа — время дороже гордости. Зову того, кто уже работал с этим стеком.
2.  **Я тупикую > 2 часов после тщательного анализа.**
    *   Правило «двух часов»: Если я уже прочитал исходники, погуглил, проверил версии, а гипотезы не подтверждаются — я, вероятно, упускаю очевидный факт (например, кэш, DNS, сеть), который виден со стороны.
3.  **Это вопрос архитектуры или бизнес-логики.**
    *   *Пример:* «Почему эта функция вызывает этот сервис?» Это знает дизайнер системы, а не тот, кто пишет код.
4.  **Второе мнение подтверждает/опровергает мою гипотезу.**
    *   *Пример:* «Я думаю, что это из-за race condition. Могу я показать код и логи?» Это эффективное использование чужого времени.

### Формула эффективного вопроса к коллеге:

> «Я разбираюсь с проблемой [Описание].
> Я уже проверил: [Список действий: логов, версий, конфигов].
> Моя текущая гипотеза: [Что ты думаешь, что это].
> Я застрял на этапе: [Конкретное место].
> Мог бы ты подсказать, что я мог упустить?»

### Итог

**Сроки решения:**
*   *Простая проблема (конфиг, версия):* 2–4 часа.
*   *Средняя (баг в логике, неочевидный):* 1–2 дня.
*   *Сложная (инфраструктура, гонки, редкие сбои):* 3–5 дней + время на валидацию.

**Главное правило:** Не тратьте время на *гадание*. Тратьте время на *сбор данных*. Как только у вас есть данные (логи, воспроизводимость, гипотеза), проблема перестает быть «новой» и становится «разбираемой».

> ⏱ 32.5s · prompt 73215 tok · output 2012 tok

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

**Кандидат:**

Это классическая ситуация, которая проверяет не вашу техническую скорость, а **навыки коммуникации и управления ожиданиями**.

Моя главная цель — **не опоздать с сообщением**, а опоздать с результатом (если это неизбежно).

Вот алгоритм моих действий:

### 1. Остановиться и честно оценить (Stop & Assess)
Я останавливаюсь на моменте, когда я уверен, что задача не впишется в дедлайн. Не жду, пока до конца дня останется час, и не надеюсь, что «сегодня ночью допишу».
*   **Чего я НЕ делаю:** Я не молча работаю сверхурочных, пытаясь «докрутить» результат. Это создает риск выгорания и низкого качества кода (спешка).

### 2. Сформулировать проблему конкретно (Define the Gap)
Прежде чем идти к заказчику/лиду, я должен четко понимать разрыв между «обещанным» и «фактическим».
*   **Было:** 1 день.
*   **Стало:** 2 дня.
*   **Почему?** (Это самое важное).
    *   *Причина A:* Я недооценил сложность (могла случиться ошибка в оценке).
    *   *Причина B:* Появились скрытые требования (например, нужно было покрыть edge-case, о котором забыли в ТЗ).
    *   *Причина C:* Технические трудности (баг в зависимости, который не описан в доках).
*   **Что уже сделано?** (Прогресс). Например: «80% логики готово, тесты проходят, осталась интеграция с внешним API, которая оказалась сложнее из-за изменения их схемы».

### 3. Предложить варианты (Options, not just Problems)
Я никогда не иду к заказчику просто с фразой «У меня не получается». Я иду с решениями.

**Вариант 1 (Рекомендуемый): Сдвиг дедлайна на завтра.**
> «Коллеги, оценка задачи [ID] была занижена из-за [Причина]. Чтобы сдать качественный код с тестами, мне потребуется 1 дополнительный день. Могу закрыть ее послезавтра (через 2 дня), но качество будет гарантировано.»

**Вариант 2 (Компромисс): Частичная сдача (MVP).**
> «Если критично сдать завтра, я могу выпустить версию A (основная логика работает, но без обработки ошибки X и без некоторых тестов). Версию B (полный функционал) закончу послезавтра. Как вам удобнее?»

**Вариант 3 (Эскалация/Помощь).**
> «Я застрял на [конкретная техническая проблема]. Если кто-то из команды может подсказать/помочь с этим пунктом, я смогу уложиться в завтрашний день.»

### 4. Связаться с заинтересованными сторонами (Communicate)
*   **Кому писать?**
    *   **Product Owner / Заказчик:** Если отсрочка влияет на бизнес-планы или релиз.
    *   **Tech Lead / Manager:** Чтобы они могли перестроить план спринта или помочь с приоритетами.
    *   **QA / Frontend / Backend-коллеги:** Если мой задержанный код блокирует их работу.
*   **Канал:** Чат (для скорости), но с последующим обновлением статуса в трекере (Jira/Trello).

### 5. Обновить трекер и план (Document)
*   Меняю статус задачи или создаю подзадачу «Остаток работ».
*   Пишу комментарий: «Переоценка. Новые сроки: [Дата]. Причина: [Кратко]».
*   Это защита от «а почему ты не сделал то, что было в спринте?».

---

### Почему это лучше, чем другие варианты?

❌ **Вариант: «Тянуть до последнего, а потом сказать, что не успел».**
*   *Последствия:* Доверие потеряно. Заказчик думал, что задача будет готова, планировал следующие шаги, а они сорвались. Вы выглядите как человек, который не умеет оценивать риски.

❌ **Вариант: «Работать в выходные, чтобы уложиться в срок».**
*   *Последствия:* Вы выгораете. Код написанный в спешке и усталости, содержит больше багов. Вы создаете прецедент, что «одна задача» может занимать 1.5 рабочих дня, и все начинают оценивать задачи с учетом ваших «ночных смен».

❌ **Вариант: «Молча сдать то, что есть, без полного функционала».**
*   *Последствия:* QA или Пользователь найдут недостающую часть. Вы получите баг вместо фичи. Доверие к качеству вашего кода упадет.

---

### Итог

**Я говорю правду как можно раньше.**

Это показывает вашу зрелость:
1.  Вы контролируете процесс.
2.  Вы уважаете время других людей.
3.  Вы фокусируетесь на качестве, а не на формальном выполнении дедлайна любой ценой.

Команда всегда лучше относится к разработчику, который говорит: *«Мне нужно еще день, вот почему, и вот что я уже сделал»*, чем к тому, который молчит, а потом извиняется за опоздание.

> ⏱ 21.1s · prompt 75268 tok · output 1257 tok

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

**Кандидат:**

Это вопрос о **пределах совершенства (Premature Optimization)** и **законе убывающей отдачи**. В реальном проекте «идеальный код» — это часто враг дедлайна.

Мой критерий «достаточно хорошо» (Good Enough) строится на **риске** и **стоимости изменения**.

Вот мой алгоритм принятия решения и инструменты баланса.

---

### 1. Три вопроса для оценки «Достаточно ли хорошо?»

Прежде чем отпустить код, я задаю себе три вопроса. Если ответы на них меня устраивают — код готов.

#### Вопрос 1: «Если этот код сломается завтра, какой будет цена?»
*   **Высокая цена (П0/P1):** Потеря денег, падение сервиса, нарушение SLA, потеря доверия клиента.
    *   *Стандарт качества:* Максимальный. Нужны строгие типы, покрытие тестами >90%, ревью двумя людьми, нагрузочное тестирование.
    *   *Пример:* Модуль оплаты.
*   **Средняя цена (P2):** Фича не работает, но есть обходной путь. Пользователь раздражен, но не уходит.
    *   *Стандарт качества:* Хороший. Основная логика покрыта тестами, чистые интерфейсы, но можно допустить некоторые технические долги.
    *   *Пример:* Отчет в админке.
*   **Низкая цена (P3):** Внутренний скрипт, одноразовый миграционный код, прототип для проверки гипотезы.
    *   *Стандарт качества:* Минимальный. Работает? Ок. Тесты — только на крайний случай. Скорость важнее красоты.
    *   *Пример:* Скрипт для переноса данных из Excel в БД.

#### Вопрос 2: «Насколько легко этот код менять в будущем?»
*   Если логика менялась 3 раза за последний месяц — она нестабильна. Значит, текущая архитектура не справляется с требованиями.
*   *Действие:* Здесь нужно потратить время на рефакторинг и внедрение паттерна (Strategy, Repository), чтобы следующие изменения были дешевле.
*   Если логика статичная (например, расчет НДС по фиксированному правилу) — сложная абстракция избыточна.

#### Вопрос 3: «Может ли кто-то другой понять этот код за 5 минут?»
*   Если для понимания нужно прочитать 300 строк, чтобы найти одну проверку — это плохо.
*   *Действие:* Разбить на методы с говорящими именами.
*   Если код самодокументируемый (через имена и структуру) — не нужно тратить время на дополнительные комментарии или переименования ради «эстетики».

---

### 2. Как я соблюдаю баланс Качество vs Скорость?

Я использую подход **«Инвестирования в качество на входе, а не на выходе»**.

#### A. «Правило одного ревьюера» (Speed)
*   Для некритичных изменений (исправление опечаток, мелкие фичи) мне достаточно одного ревьюера или даже само-ревью + CI.
*   Для критичных (изменение API, миграции БД) — минимум два ревьюера.
*   *Почему:* Двойное ревью увеличивает время цикла с 2 часов до 8. Нужно платить за это только там, где риск высокий.

#### B. Покрытие тестами (Quality)
Я не гонюсь за 100% покрытием строк (Line Coverage). Я гонюсь за **покрытием бизнес-правил**.
*   **Что тестирую всегда:**
    1.  Доменные инварианты (например, `Money` не может быть отрицательным).
    2.  Границы (null, empty array, максимальное значение int).
    3.  Исключения (что происходит при ошибке БД или API).
*   **Что НЕ тестирую (или тестирую слабо):**
    1.  Getter/Setter (если они простые).
    2.  Стандартные маппинги ORM (если доверяю Doctrine).
    3.  Внутренние детали алгоритмов, которые не влияют на результат (если алгоритм стабильный).

#### C. Технический долг как осознанный выбор
Если нужно срочно исправить баг в продакшене, я делаю **минимальный фикс**.
*   Я не перепроектирую модуль, пока чиню аварийку.
*   Но я создаю тикет `TECH-DEBT: Refactor PaymentModule` с оценкой.
*   *Почему:* Если я начну рефакторить в момент пожара, я могу потушить одно пламя и задуть другое.

#### D. «Stop the Line» (Остановка конвейера)
Если я вижу, что код становится слишком сложным *в процессе написания* (метод разрастается, появляются вложенные if-else depth 5):
*   Я останавливаюсь.
*   Переименовываю переменные.
*   Выношу логику в отдельный метод.
*   Проверяю, не нужен ли здесь паттерн.
*   *Почему:* Улучшить код во время написания в 10 раз дешевле, чем переписывать его через 2 недели, когда на него напишут 500 строк зависимостей.

---

### 3. Конкретные метрики «Достаточно»

| Критерий | Минимум (Прототип) | Стандарт (Продакшен) | Максимум (Критично) |
| :--- | :--- | :--- | :--- |
| **Логика** | Работает в 95% случаев | Покрыты все edge cases | Покрыты concurrency, failure cases |
| **Тесты** | Ручная проверка | Юнит-тесты на домен | Интеграционные + Нагрузочные |
| **Документация** | Нет | API Doc (Swagger/Postman) | Полный мануал + ADR (Architecture Decision Records) |
| **Ревью** | Автор + CI | Автор + 1 Reviewer | Автор + 2 Reviewers + Architect |
| **Код** | Читается | Понятен, чистые методы | Идеальная инкапсуляция, полиморфизм |

---

### 4. Пример из жизни: «Калькулятор скидок»

**Ситуация:** Нужно написать логику расчета скидки для нового типа клиента.

**Вариант «Скорость» (MVP):**
```php
public function calculate($price, $type) {
    if ($type == 'vip') return $price * 0.8;
    if ($type == 'partner') return $price * 0.9;
    return $price;
}
```
*   *Когда оправдан:* Если это первая версия, и завтра мы добавим `enterprise`.
*   *Риск:* При добавлении 5-го типа это станет адом.

**Вариант «Качество» (Production):**
Интерфейс `DiscountStrategy`, классы `VipDiscount`, `PartnerDiscount`, реестр стратегий.
*   *Когда оправдан:* Если скидка — ключевой продукт, типы часто меняются, и над этим кодом работают 3+ разработчика.
*   *Цена:* 2 часа работы вместо 20 минут.

**Моё решение:**
Я смотрю на **частоту изменений**.
*   Если бизнес говорит: «Скидки меняем каждые 2 недели» → Делаю Вариант «Качество».
*   Если бизнес говорит: «Это разовая акция на неделю» → Делаю Вариант «Скорость» и ставлю тикет «Рефакторинг после окончания акции».

### Итог

Код «достаточно хорош», когда:
1.  Он решает текущую бизнес-задачу.
2.  Он не создает *новых* рисков (баги, ошибки, долг), которые стоят дороже, чем сэкономленное время на написание.
3.  Команда согласна с этим компромиссом (через ревью).

**Главное:** Не стремитесь к идеалу. Стремитесь к **предсказуемому результату**.

> ⏱ 31.0s · prompt 76578 tok · output 1913 tok

---
