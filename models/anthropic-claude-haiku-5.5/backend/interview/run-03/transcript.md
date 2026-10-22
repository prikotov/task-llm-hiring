# Собес: `anthropic/claude-haiku-5.5` · роль: backend — run-03

- Дата: 2026-10-09T01:31:28.842066+00:00
- Backend: OpenRouter · reasoning effort: low
- Вопросов: 35
- **Длительность сессии: 407s (6.8 мин)**
- Токены: prompt 1,583,849 · output 74,888 · стоимость $0.0617

---

## Q00 · Разминка

**Интервьюер:** Ты проходишь собеседование на роль PHP-бэкенд-разработчика. Начнём со знакомства. Расскажи о своём опыте в разработке на PHP и смежных технологиях. В конце оцени по шкале от 0 до 10 свои знания по каждому разделу: PHP 8, Symfony, Doctrine, PostgreSQL, DDD, чистая архитектура, тестирование, git — и одним предложением объясни каждую оценку.

**Кандидат:**

Здравствуйте! Спасибо, что пригласили. Небольшая оговорка: я ИИ, у меня нет реального опыта работы, так что ниже ответ от лица условного кандидата, который может использоваться как образец для подготовки.

**Опыт**

Я около четырёх лет разрабатываю backend на PHP. Последние два года основной стек: PHP 8.x, Symfony, Doctrine ORM и PostgreSQL. Я писал REST API для внутренних сервисов и публичных клиентских приложений, проектировал схемы БД и миграции, настраивал очереди через Symfony Messenger, а также занимался оптимизацией медленных запросов через EXPLAIN и правильные индексы. В командах я отвечаю за код-ревью, работаю через git-flow и pull request'ы и стараюсь поддерживать покрытие ключевых сценариев тестами. Пробовал применять принципы DDD в модуле заказов: выделял агрегаты и доменные события. Чистую архитектуру использую как ориентир для разделения слоёв, но на практике иногда иду на компромиссы ради скорости delivery.

**Самооценка**

- **PHP 8: 8/10.** Уверенно использую enum, readonly-свойства, named arguments, match, union и intersection types, но не всегда успеваю отслеживать новые возможности релиз за релизом.
- **Symfony: 8/10.** Знаю DI-контейнер, событийную модель, Security, Messenger и Console, однако глубже всего разбираюсь в типовых задачах, а не во внутренней кухне ядра.
- **Doctrine: 7/10.** Уверенно работаю с маппингом, связями и DQL, но в сложных случаях (кастомные гидраторы, тонкая настройка unit of work, производительность при большом объёме) мне нужно время на изучение.
- **PostgreSQL: 7/10.** Умею проектировать схемы, писать запросы с CTE и оконными функциями, читать планы выполнения, но в тонкостях вроде настройки autovacuum и партиционирования опыта меньше.
- **DDD: 6/10.** Понимаю тактические паттерны (агрегаты, value objects, доменные события) и применял их на практике, но стратегический дизайн с bounded contexts у меня пока в основном теоретический.
- **Чистая архитектура: 6/10.** Понимаю идею зависимостей, направленных внутрь, и умею её применять, но на практике часто упрощаю границы слоёв ради сроков.
- **Тестирование: 7/10.** Пишу unit- и интеграционные тесты с PHPUnit и понимаю пирамиду тестов, но end-to-end и тесты с контейнером иногда делаю неаккуратно.
- **Git: 8/10.** Уверенно работаю с ветками, rebase, интерактивным rebase, разрешением конфликтов и историей коммитов, хотя с нетривиальным восстановлением после ошибок иногда сверяюсь с документацией.

Если хотите, могу продолжить интервью: например, задать технические вопросы по Doctrine или PostgreSQL, либо разобрать кейс проектирования API.

> ⏱ 9.3s · prompt 165 tok · output 1272 tok · $0.0007

---

## Q01 · PHP 8

**Интервьюер:** Какие возможности современных версий PHP — с 8.0 и новее — ты считаешь самыми важными для написания чистого и надёжного кода? Покажи на коротких примерах кода.

**Кандидат:**

Ниже подборка возможностей PHP 8.0+, которые, по моему опыту, сильнее всего влияют на чистоту и надёжность кода. Примеры короткие, но их можно запускать.

**1. Конструктор с продвижением свойств и readonly (8.0 / 8.1)**

Уменьшает шум и делает объекты неизменяемыми там, где это нужно.

```php
final class Money
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency,
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException('Сумма не может быть отрицательной');
        }
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new DomainException('Валюты не совпадают');
        }
        return new self($this->amount + $other->amount, $this->currency);
    }
}

$price = new Money(1000, 'RUB');
// $price->amount = 0; // Error: Cannot modify readonly property
```

**2. Enum (8.1)**

Заменяет строковые и числовые константы типобезопасным типом с возможностью добавлять методы.

```php
enum OrderStatus: string
{
    case New = 'new';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';

    public function canBeCancelled(): bool
    {
        return match ($this) {
            self::New, self::Paid => true,
            self::Shipped, self::Cancelled => false,
        };
    }
}

$status = OrderStatus::from('paid');
var_dump($status->canBeCancelled()); // bool(true)
```

**3. match вместо switch (8.0)**

Строгое сравнение, возвращает значение и выбрасывает исключение, если ветка не найдена. Это защищает от забытых случаев.

```php
$discount = match ($customerType) {
    'vip'      => 0.20,
    'regular'  => 0.05,
    'guest'    => 0.0,
    default    => throw new InvalidArgumentException("Неизвестный тип: $customerType"),
};
```

**4. Union, nullable и intersection types, static и never (8.0–8.1)**

Типы на границах функций дают раннюю проверку ошибок.

```php
function findUserId(string|int $identifier): ?int
{
    // ...
}

function fail(string $message): never
{
    throw new RuntimeException($message);
}

function countAll(Countable&Traversable $items): int
{
    return count($items);
}
```

**5. Named arguments (8.0)**

Делают вызовы с необязательными параметрами читаемыми и защищают от перепутанного порядка.

```php
$response = new JsonResponse(
    data: $payload,
    status: 201,
    headers: ['Cache-Control' => 'no-store'],
);
```

**6. Nullsafe-оператор (8.0)**

Убирает цепочки проверок на null.

```php
$city = $order?->getCustomer()?->getAddress()?->getCity();
```

**7. First-class callable syntax (8.1)**

Короткий и явный способ получить callable.

```php
$lengths = array_map(strlen(...), $names);

final class PriceCalculator
{
    public function applyVat(float $price): float
    {
        return $price * 1.2;
    }
}

$calc = new PriceCalculator();
$withVat = array_map($calc->applyVat(...), $prices);
```

**8. Новые функции и поведение (8.1–8.4)**

- `array_is_list()` (8.1) проверяет, что массив индексирован последовательно от нуля.
- Fibers (8.1) дают кооперативную многозадачность, полезны для фреймворков и асинхронных библиотек, а в прикладном коде используются редко.
- Typed class constants (8.3) и `#[\Override]` (8.3) помогают ловить ошибки при наследовании: атрибут падает, если метод в родителе не существует.
- Property hooks (8.4) позволяют задавать логику чтения и записи свойств прямо в объявлении.

**Что я считаю главным**

Из всего списка наибольшую пользу для надёжности дают типы (включая `never` и `readonly`), enum и `match`. Они переносят проверки из рантайма в объявления, и компилятор или статический анализатор (PHPStan, Psalm) начинают ловить ошибки до запуска. Named arguments и nullsafe улучшают читаемость, но не влияют на надёжность так сильно.

> ⏱ 8.3s · prompt 1324 tok · output 1551 tok · $0.0009

---

## Q02 · PHP 8

**Интервьюер:** Как устроена система типов в PHP: что даёт strict_types, как ведут себя приведения типов, какие типичные ловушки встречаются на практике? Приведи примеры, где типизация спасает, а где удивляет.

**Кандидат:**

## Как устроена система типов

PHP динамически типизирован, но с 7.0 поддерживает объявления типов для параметров, возвращаемых значений и свойств. Проверка выполняется в рантайме при каждом вызове. Важно, что **тип проверяется на границе функции**, а внутри неё значение может менять тип только через явные операции или приведение.

Есть два режима поведения скалярных типов (`int`, `float`, `string`, `bool`):

- **Coercive** (по умолчанию): PHP пытается привести значение к нужному типу.
- **Strict** (`declare(strict_types=1)`): принимаются только точные типы, за одним исключением: `int` можно передать туда, где ожидается `float`.

## strict_types

Директива действует **на уровне файла, в котором сделан вызов**, для вызовов функций и методов, а также для возвращаемых значений функций, объявленных в этом файле. Это частая путаница: режим определяется не файлом, где находится функция, а файлом вызывающего кода.

```php
// file: Calc.php (без strict_types)
function add(int $a, int $b): int
{
    return $a + $b;
}
```

```php
// file: main.php
declare(strict_types=1);

require 'Calc.php';

echo add(1, 2);      // 3
echo add("1", 2);    // TypeError: вызов из strict-файла
```

Если в `Calc.php` объявлен `strict_types=1`, а вызов идёт из нестрогого файла, `"1"` будет приведено к `1`. То есть строгость не «наследуется» функцией.

Также стоит помнить, что строгий режим не влияет на встроенные функции иначе, чем на пользовательские: `strlen(123)` в strict-файле бросит `TypeError`, в нестрогом вернёт `3`.

## Правила приведения в coercive-режиме

| Ожидается | Передано | Результат |
|---|---|---|
| `int` | `"42"` | `42` |
| `int` | `"42abc"` | `42` с предупреждением (leading-numeric) |
| `int` | `"abc"` | `TypeError` |
| `int` | `42.0` | `42` |
| `int` | `42.5` | приведение с deprecation с PHP 8.1 |
| `string` | `42` | `"42"` |
| `bool` | `0`, `"0"`, `""`, `"abc"` | `false`, `false`, `false`, `true` |
| `float` | `"3.14"` | `3.14` |
| любой скалярный | `null` | `TypeError` в пользовательских функциях |

Последнее правило важно: `null` не приводится к скалярным типам в пользовательском коде. Но во встроенных функциях `strlen(null)` в PHP 8.1+ даёт deprecation, а не ошибку, поэтому такие вызовы могут тихо проходить.

## Где типизация спасает

**1. Ранняя ошибка на границе.**

```php
declare(strict_types=1);

final class Order
{
    public function __construct(private int $totalCents) {}
}

new Order("1999.99"); // TypeError сразу, а не через неделю в отчёте
```

**2. Enum и match ловят забытые случаи.**

```php
enum Status: string { case Paid = 'paid'; case New = 'new'; }

$label = match (Status::from('paid')) {
    Status::Paid => 'Оплачен',
    // без ветки для New получим UnhandledMatchError
};
```

**3. `never` и `readonly` закрепляют намерение.** Функция с `never` гарантированно не возвращает управление, а `readonly`-свойство нельзя изменить после инициализации.

## Где типизация удивляет

**1. Coercive-режим молча переделывает данные.**

```php
function setPort(int $port): void { var_dump($port); }

setPort("8080abc"); // int(8080) с предупреждением, а не ошибка
setPort(true);      // int(1)
```

Без `strict_types` строка `"0"` и `false` оба приводятся к `bool` в `false`, а `true` в `int` становится `1`. Такие данные из формы или `$_GET` лучше валидировать явно.

**2. Сравнение `==` и `switch`.** PHP 8 исправил часть ошибок, но не все.

```php
var_dump(0 == "a");        // false в PHP 8 (раньше было true)
var_dump("1" == "01");     // true
var_dump("10" == "1e1");   // true
var_dump(100 == "1e2");    // true
var_dump(null == false);   // true
var_dump([] == false);     // true

$code = "0";
switch ($code) {           // switch использует нестрогое сравнение
    case 0:  echo "ноль";  break;   // сработает
    case "": echo "пусто"; break;
}
```

Правило простое: всегда используйте `===` и `!==`.

**3. `in_array` без `strict`.**

```php
var_dump(in_array(null, [''])); // true
var_dump(in_array('abc', [0])); // false в PHP 8, но тот же вызов в PHP 7 был true

var_dump(in_array('1', [1], true)); // false, а с третьим аргументом сравнение строгое
```

**4. Ключи массивов тоже приводятся.**

```php
$a = [];
$a["1"] = 'a';    // ключ станет int 1
$a["01"] = 'b';   // останется строкой "01"
$a[true] = 'c';   // ключ 1, перезапишет 'a'
$a[1.7] = 'd';    // ключ 1 с deprecation в 8.1+
```

**5. Float и деньги.**

```php
var_dump(0.1 + 0.2 == 0.3); // false
```

Для денег храните копейки в `int`, а не `float`.

**6. Переполнение `int` не выбрасывает ошибку.**

```php
var_dump(PHP_INT_MAX + 1); // float(9.2233720368547758E+18)
```

Тип меняется молча. Для точных вычислений используйте `bcmath` или `GMP`, а при критичных проверках — явные границы.

**7. Nullable-параметры с дефолтом `null`.**

```php
function f(int $x = null) {} // неявно nullable, в PHP 8.4 deprecation
function g(?int $x = null) {} // явно и правильно
```

**8. Типизированные свойства и доступ до инициализации.**

```php
class User { public int $id; }

$u = new User();
echo $u->id; // Error: must not be accessed before initialization
```

Это ошибка, а не `null`, поэтому в ORM с ленивой загрузкой и гидрацией такие случаи нужно учитывать.

**9. Типы не покрывают данные извне.** `json_decode` возвращает `array` или `stdClass` без типов внутри. Типы на параметрах гарантируют только то, что вы передали, но не структуру данных. Для этого нужны DTO с конструктором и явной валидацией, например через Symfony Validator или отдельный маппинг.

## Практические рекомендации

- Всегда объявляйте `declare(strict_types=1)` в каждом файле.
- Типизируйте параметры, возвращаемые значения и свойства.
- Используйте `===` везде, где не требуется осознанное нестрогое сравнение.
- На границе системы (HTTP, CLI, файлы, БД) приводите данные к типам явно и валидируйте их до передачи в домен.
- Денежные суммы храните в минимальных единицах в `int`.
- Подключите PHPStan или Psalm на высоком уровне: большинство ловушек из этого списка они находят статически.

> ⏱ 22.3s · prompt 2951 tok · output 4370 tok · кэш 1 320+1 627 tok · $0.0024

---

## Q03 · PHP 8

**Интервьюер:** Как ты проектируешь работу с исключениями в PHP-приложении: свои иерархии исключений, оборачивание чужих исключений, что считается контрактом метода? Какие антипаттерны здесь встречаются?

**Кандидат:**

Исключения для меня часть публичного контракта метода, поэтому я проектирую их так же осознанно, как сигнатуры и типы.

## Базовая идея

Исключение означает, что метод не может выполнить то, что обещал по контракту. Оно не заменяет `if` для ожидаемых ситуаций, например «пользователь не найден при поиске по email на форме входа». Для таких случаев я возвращаю `null`, `Optional`-подобный результат или отдельный тип ответа. Исключение нужно там, где вызывающий код не может продолжить работу без вмешательства.

## Иерархия исключений

Иерархия нужна, чтобы вызывающий код ловил группу ошибок, а не строки или коды. Я обычно делаю корневой интерфейс библиотеки или модуля и от него развожу ветки по смыслу, а не по месту, где возникла ошибка.

```php
namespace App\Billing\Exception;

interface BillingException extends \Throwable {}

// Ошибка бизнес-правила: повторять запрос бессмысленно
class InsufficientFundsException extends \RuntimeException implements BillingException
{
    public static function forAccount(int $accountId, int $required, int $available): self
    {
        return new self(sprintf(
            'Account %d: required %d, available %d',
            $accountId, $required, $available,
        ));
    }
}

// Ошибка инфраструктуры: повтор может помочь
class PaymentGatewayUnavailableException extends \RuntimeException implements BillingException {}

// Ошибка программиста: неверные аргументы или состояние
class InvalidPaymentStateException extends \LogicException implements BillingException {}
```

Здесь важно разделение `RuntimeException` и `LogicException`. Первое ожидаемо может случиться в рантайме, второе обычно означает баг в вызывающем коде.

Named constructor (`forAccount`) даёт место для формирования сообщения и не даёт разбрасывать `sprintf` по коду.

## Оборачивание чужих исключений

Чужие исключения (из Doctrine, HTTP-клиента, драйвера PDO) не должны просачиваться через границу модуля. Иначе домен или прикладной слой начинают зависеть от реализации инфраструктуры, и замена библиотеки ломает все вызовы.

Оборачиваю с сохранением цепочки через `previous`:

```php
namespace App\Billing\Infrastructure;

use App\Billing\Exception\PaymentGatewayUnavailableException;
use GuzzleHttp\Exception\GuzzleException;

final class HttpPaymentGateway
{
    public function __construct(private \GuzzleHttp\ClientInterface $client) {}

    public function charge(string $token, int $amount): void
    {
        try {
            $this->client->request('POST', '/charge', ['json' => compact('token', 'amount')]);
        } catch (GuzzleException $e) {
            throw new PaymentGatewayUnavailableException(
                'Gateway request failed',
                previous: $e,
            );
        }
    }
}
```

Так оригинальный стек не теряется (`$e->getPrevious()` и логирование его показывают), а наружу уходит своё исключение.

Не оборачиваю всё подряд. Если исключение уже относится к моему типу или сообщает то, что вызывающему коду полезно знать точно, я его пропускаю. Лишняя обёртка без смысла только усложняет трассировку.

## Что считается контрактом метода

Контракт включает:

- типы параметров и возвращаемого значения;
- предусловия, при нарушении которых бросается `InvalidArgumentException` или `LogicException`;
- набор исключений, которые метод может бросить в штатной работе, описанный в PHPDoc через `@throws`.

```php
/**
 * @throws InsufficientFundsException если на счёте недостаточно средств
 * @throws PaymentGatewayUnavailableException если шлюз недоступен
 */
public function withdraw(Account $account, int $amount): void
```

PHP не проверяет `@throws` компилятором, поэтому PHPStan с правилом на аннотации или ручная дисциплина на ревью здесь обязательны. Я также явно разделяю, что метод гарантирует при ошибке: например, не остаётся ли он в частично изменённом состоянии. Если транзакция откатывается, это тоже часть контракта.

Никогда не бросаю `\Exception` напрямую и не ловлю `\Exception` или `\Throwable` без причины на границе, где нужно залогировать и вернуть безопасный ответ.

## Антипаттерны

**1. Проглатывание исключений.**

```php
try {
    $this->mailer->send($message);
} catch (\Throwable $e) {
    // ничего
}
```

Ошибка исчезает, и её невозможно диагностировать. Если исключение действительно не важно, нужно хотя бы залогировать его с контекстом и объяснить это в комментарии.

**2. Потеря `previous`.**

```php
} catch (PDOException $e) {
    throw new RepositoryException('Ошибка БД'); // исходная причина потеряна
}
```

**3. Исключения для управления потоком.** Бросать исключение, чтобы выйти из цикла или отметить «не найдено» для обычного сценария, дорого и запутывает. Исключения должны быть исключительными.

**4. Ловля слишком широко.** `catch (\Exception $e)` вокруг большого блока ловит и ожидаемую ошибку, и баг программиста (`TypeError` и подобные из `Error`, которые не входят в `Exception`, так что `catch (\Exception)` их не поймает, но `catch (\Throwable)` поймает). Ловлю конкретные типы.

**5. Ошибки, спрятанные в коде возврата.** `return false` или `return null` при сбое вместо исключения. Вызывающий код легко забывает проверить. Исключение здесь честнее, если сбой реальный.

**6. Сообщение вместо типа.** Логика вида `if (str_contains($e->getMessage(), 'duplicate'))` хрупка. Нужен отдельный класс исключения или проверка кода SQLSTATE через типизированную обёртку.

**7. `return` или `throw` в `finally`.** `return` в `finally` молча отменяет исключение из блока `try`. Очистку ресурсов делаю в `finally`, но без возврата значения.

**8. Информация для пользователя внутри исключения.** Сообщение исключения попадает в логи и отладку. Текст для пользователя формирую на границе (контроллер, exception listener), а не в домене.

**9. Утечки деталей наружу.** В HTTP-ответе не должно быть трассировки, SQL-запроса или имени внутреннего класса. В Symfony я маплю доменные исключения на статусы через `ExceptionListener` или `#[AsEventListener]` с понятным ответом, а неизвестные `Throwable` превращаю в 500 с логированием.

**10. Бросать `Exception` из конструктора при частично инициализированном объекте.** Объект не должен существовать в невалидном состоянии. Проверки в конструкторе или named constructor, который не возвращает полуготовый экземпляр.

## Итог

Я стараюсь, чтобы исключения были типизированными, принадлежали модулю, сохраняли причину, документировались в `@throws` и обрабатывались там, где есть контекст для решения. Всё остальное, включая логирование и формирование ответа для пользователя, остаётся на границах приложения.

> ⏱ 16.3s · prompt 5648 tok · output 2935 tok · кэш 2 947+2 697 tok · $0.0018

---

## Q04 · ООП и принципы

**Интервьюер:** Какой из принципов SOLID, по твоему опыту, чаще всего нарушается в реальном коде и почему? Покажи типовой пример нарушения и его рефакторинг.

**Кандидат:**

## Какой принцип нарушается чаще всего

По моему опыту, чаще всего нарушается **Single Responsibility Principle (SRP)**: класс должен иметь одну причину для изменения. На практике это выглядит так: сервис `OrderService` валидирует данные, считает цену, сохраняет в БД, отправляет письмо и пишет лог. Изменение шаблона письма, правило скидки и смена ORM заставляют трогать один и тот же класс.

Причины, по которым SRP нарушают так часто:

- **Удобство.** Всё нужное уже есть в контроллере или сервисе, проще дописать метод, чем создать новый класс.
- **Сроки.** Фича «ещё отправить письмо» добавляется в существующий метод, а не в подписчик на событие.
- **Размытые границы.** В Symfony-проектах Doctrine, Mailer и HTTP смешиваются в сервисах, и граница между прикладным и инфраструктурным кодом не видна.
- **Тесты.** Чтобы проверить расчёт скидки, приходится мокать почту, EntityManager и логгер. Это симптом нарушения.

## Типовой пример нарушения

```php
final class OrderService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MailerInterface $mailer,
        private LoggerInterface $logger,
    ) {}

    public function placeOrder(array $data): void
    {
        // 1. Валидация
        if (empty($data['items']) || !is_array($data['items'])) {
            throw new InvalidArgumentException('Пустой заказ');
        }
        if (!filter_var($data['email'] ?? null, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Неверный email');
        }

        // 2. Расчёт цены и бизнес-правило скидки
        $total = 0;
        foreach ($data['items'] as $item) {
            $total += $item['price'] * $item['qty'];
        }
        if (($data['coupon'] ?? null) === 'SALE10') {
            $total = (int) round($total * 0.9);
        }

        // 3. Сохранение
        $order = new Order($data['email'], $total);
        $this->em->persist($order);
        $this->em->flush();

        // 4. Отправка письма с версткой прямо в сервисе
        $html = sprintf('<h1>Заказ #%d</h1><p>Сумма: %d</p>', $order->getId(), $total);
        $this->mailer->send((new Email())
            ->to($data['email'])
            ->subject('Ваш заказ')
            ->html($html));

        // 5. Логирование
        $this->logger->info('Order placed', ['id' => $order->getId()]);
    }
}
```

Проблемы: класс меняется по пяти причинам, тесты скидки тянут почту и БД, а при изменении формата входных данных (например, с массива на HTTP-запрос) затрагивается вся логика.

## Рефакторинг

### 1. Входные данные — типизированная команда с валидацией

Сервис получает объект, а не сырой массив. Разбор `$data` остаётся на границе.

```php
final readonly class PlaceOrderCommand
{
    /**
     * @param list<OrderItemDto> $items
     */
    public function __construct(
        public string $email,
        public array $items,
        public ?string $couponCode = null,
    ) {
        if ($items === []) {
            throw new InvalidArgumentException('Пустой заказ');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Неверный email');
        }
    }
}

final readonly class OrderItemDto
{
    public function __construct(public int $priceCents, public int $quantity) {}
}
```

### 2. Расчёт цены — отдельный сервис без инфраструктуры

```php
interface DiscountPolicy
{
    public function apply(int $totalCents, ?string $couponCode): int;
}

final class CouponDiscountPolicy implements DiscountPolicy
{
    public function apply(int $totalCents, ?string $couponCode): int
    {
        return $couponCode === 'SALE10'
            ? (int) round($totalCents * 0.9)
            : $totalCents;
    }
}

final readonly class OrderPriceCalculator
{
    public function __construct(private DiscountPolicy $discounts) {}

    public function calculate(PlaceOrderCommand $command): int
    {
        $subtotal = 0;
        foreach ($command->items as $item) {
            $subtotal += $item->priceCents * $item->quantity;
        }

        return $this->discounts->apply($subtotal, $command->couponCode);
    }
}
```

Теперь правило скидки тестируется без моков и без `EntityManager`.

### 3. Сохранение — через репозиторий

```php
interface OrderRepository
{
    public function add(Order $order): void;
}

final class DoctrineOrderRepository implements OrderRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function add(Order $order): void
    {
        $this->em->persist($order);
        $this->em->flush();
    }
}
```

### 4. Побочные эффекты — через событие

Сервис публикует факт, а не решает, кто и как на него реагирует.

```php
final readonly class OrderPlaced
{
    public function __construct(public int $orderId, public string $email, public int $totalCents) {}
}

final readonly class PlaceOrderHandler
{
    public function __construct(
        private OrderPriceCalculator $calculator,
        private OrderRepository $orders,
        private EventDispatcherInterface $events,
    ) {}

    public function __invoke(PlaceOrderCommand $command): void
    {
        $total = $this->calculator->calculate($command);

        $order = new Order($command->email, $total);
        $this->orders->add($order);

        $this->events->dispatch(new OrderPlaced($order->getId(), $command->email, $total));
    }
}

final readonly class SendOrderConfirmationListener
{
    public function __construct(private MailerInterface $mailer) {}

    #[AsEventListener]
    public function __invoke(OrderPlaced $event): void
    {
        $this->mailer->send((new Email())
            ->to($event->email)
            ->subject('Ваш заказ')
            ->html(sprintf('<h1>Заказ #%d</h1><p>Сумма: %d</p>', $event->orderId, $event->totalCents)));
    }
}

final readonly class LogOrderPlacedListener
{
    public function __construct(private LoggerInterface $logger) {}

    #[AsEventListener]
    public function __invoke(OrderPlaced $event): void
    {
        $this->logger->info('Order placed', ['id' => $event->orderId]);
    }
}
```

## Что получилось

- `PlaceOrderHandler` меняется только при изменении сценария оформления.
- Скидки меняются в `DiscountPolicy`, не затрагивая сохранение.
- Письмо и лог подключаются и отключаются подписчиками без правок в обработчике.
- Тест обработчика проверяет один сценарий, а тест калькулятора не требует БД и почты.

Важно: дробить класс ради дробления не нужно. Если сценарий маленький и стабильный, отдельные классы на каждую мелочь создают шум. Критерий простой: если у класса появилось две независимые причины меняться, пора разделять.

### ↳ Углубление

**Интервьюер:** Можно ли нарушать принципы SOLID? Приведи пример оправданного нарушения.

**Кандидат:**

Да, и более того: SOLID задумывался как набор эвристик, а не законов. Принцип полезен, пока его соблюдение снижает стоимость изменений и тестирования. Если соблюдение усложняет код без выгоды, нарушение оправдано, но его нужно осознавать и фиксировать.

Важно различать два случая: нарушение **по незнанию** (код просто плохо спроектирован) и **осознанное** (есть конкретная причина, и цена решения понятна). Только второе можно назвать оправданным.

## Пример оправданного нарушения

Допустим, в проекте есть простой сервис конвертации валюты для отчётов. Курс берётся из одного источника, правила стабильны, а команда из двух человек.

Формально SRP и инверсия зависимостей требуют:

- интерфейс `ExchangeRateProvider`;
- реализацию `CbrExchangeRateProvider`;
- интерфейс `CurrencyConverter` и отдельный `RoundingPolicy`;
- фабрику, связывающую всё это.

Для задачи, которая сводится к одной функции, это лишние пять файлов и абстракции без второй реализации.

Осознанный вариант:

```php
final readonly class CurrencyConverter
{
    public function __construct(private \GuzzleHttp\ClientInterface $http) {}

    public function convert(int $amountKopecks, string $from, string $to): int
    {
        $rate = $this->fetchRate($from, $to);

        return (int) round($amountKopecks * $rate);
    }

    private function fetchRate(string $from, string $to): float
    {
        // прямой вызов внешнего API, без абстракции источника
        // ...
    }
}
```

Здесь класс нарушает принцип инверсии зависимостей: он зависит от конкретного HTTP-клиента внутри приватного метода и от конкретного способа получения курса. Это сознательно, потому что:

- второго источника курса нет и в ближайших планах не предвидится;
- тестирование решается мокированием HTTP-клиента на уровне Guzzle (MockHandler), без отдельного интерфейса;
- абстракция сейчас ничего не покупает, а её стоимость (файлы, навигация, DI-конфигурация) реальна.

## Когда нарушение перестаёт быть оправданным

Тот же код станет проблемой, если:

- появится второй провайдер курса или нужно переключаться между ними по конфигурации;
- метод `convert` начнёт обрастать ветками по источникам;
- тесты станут хрупкими из-за деталей HTTP-клиента;
- класс начнёт менять курс под разные правила округления для разных клиентов.

В этот момент выделение интерфейса `ExchangeRateProvider` становится оправданным, и делать его стоит быстро, а не откладывать.

## Практическое правило

Нарушение оправдано, если выполняются три условия:

1. Есть конкретная выгода, которую дают только текущие упрощения.
2. Стоимость соблюдения принципа сейчас выше, чем вероятная стоимость его нарушения.
3. Решение зафиксировано: в коде комментарием или ADR, а в ревью понятно, что это намеренно.

Проблема в том, что «оправданные» нарушения часто оказываются просто ленью. Поэтому я проверяю себя простым вопросом: что конкретно сломается или подорожает, если я сделаю по принципу? Если ответ «ничего» или «не знаю», значит, принцип стоит соблюсти.

### ↳ Углубление

**Интервьюер:** OCP можно реализовать способами, кроме наследования — какими?

**Кандидат:**

Open/Closed Principle формулируется как «открыт для расширения, закрыт для модификации». Наследование — самый очевидный способ, но в PHP и в современных проектах чаще используют другие механизмы. Ниже основные.

## 1. Композиция и стратегии (интерфейс + несколько реализаций)

Самый распространённый способ. Класс принимает зависимость через интерфейс, и новое поведение добавляется новой реализацией, без правки клиента.

```php
interface ShippingCost
{
    public function calculate(Order $order): int;
}

final class FlatRateShipping implements ShippingCost
{
    public function calculate(Order $order): int { return 300; }
}

final class FreeShippingOverThreshold implements ShippingCost
{
    public function calculate(Order $order): int
    {
        return $order->totalCents() >= 300_000 ? 0 : 300;
    }
}

final readonly class CheckoutCalculator
{
    public function __construct(private ShippingCost $shipping) {}

    public function total(Order $order): int
    {
        return $order->totalCents() + $this->shipping->calculate($order);
    }
}
```

Новый способ доставки — новый класс, `CheckoutCalculator` не меняется.

## 2. Тегированные коллекции сервисов (Symfony)

Если набор стратегий должен расти сам, а клиент выбирает подходящую, удобно собирать их через DI-тег и выбирать первую, которая подходит.

```php
interface PaymentMethod
{
    public function supports(string $type): bool;
    public function pay(Money $amount): void;
}

final readonly class PaymentRouter
{
    /** @param iterable<PaymentMethod> $methods */
    public function __construct(private iterable $methods) {}

    public function pay(string $type, Money $amount): void
    {
        foreach ($this->methods as $method) {
            if ($method->supports($type)) {
                $method->pay($amount);
                return;
            }
        }
        throw new DomainException("Метод оплаты не поддерживается: $type");
    }
}
```

В `services.yaml` достаточно пометить реализации тегом `app.payment_method` (или использовать `#[AutoconfigureTag]`), и новые методы подхватываются автоматически.

## 3. Событийная модель (Observer / Dispatcher)

Ядро публикует факт, а расширения подписываются на него. Так добавляется реакция без изменения отправителя.

```php
final readonly class OrderPlaced { public function __construct(public int $orderId) {} }

#[AsEventListener]
final readonly class NotifyWarehouse
{
    public function __invoke(OrderPlaced $event): void { /* ... */ }
}
```

Важно: события расширяют поведение только в точках, которые ядро заранее объявило. Если такой точки нет, понадобится правка.

## 4. Декоратор

Оборачивает существующую реализацию и добавляет поведение (кэш, логирование, метрики), не меняя её код.

```php
final readonly class CachedExchangeRates implements ExchangeRates
{
    public function __construct(
        private ExchangeRates $inner,
        private CacheInterface $cache,
    ) {}

    public function rate(string $from, string $to): float
    {
        return $this->cache->get("rate_{$from}_{$to}", fn () => $this->inner->rate($from, $to));
    }
}
```

## 5. Замыкания и callable-политики

Для простых правил не нужны классы: достаточно передать функцию.

```php
final class Paginator
{
    /** @param \Closure(Item): bool $filter */
    public function filter(array $items, \Closure $filter): array
    {
        return array_values(array_filter($items, $filter));
    }
}
```

## 6. Паттерн «Цепочка обязанностей»

Обработчики выстраиваются в цепочку, и каждый либо обрабатывает запрос, либо передаёт дальше. Новый обработчик добавляется в конец или в нужное место конфигурации. Подходит для валидации, проверок доступа, обработки входящих сообщений.

## 7. Конфигурация и данные вместо кода

Правила хранятся в таблице, JSON или конфиге, а движок их интерпретирует. Добавление правила не требует кода, но сложность переносится в данные, поэтому пользоваться этим стоит аккуратно: язык правил со временем обрастает своими проблемами.

## 8. Полиморфизм через трейты и mixins

Трейты дают повторное использование кода, но для OCP они слабее интерфейсов: поведение подмешивается статически, и граница расширения менее явна. Использую их для переиспользуемых фрагментов реализации, а не как основной механизм расширения.

## Практический вывод

Обычно комбинируют 1 (интерфейс и стратегия) с 2 (теги в DI) или 3 (события). Наследование я оставляю для случаев, где есть реальная иерархия типов. Главный критерий: точка расширения должна быть заложена заранее там, где вероятны изменения. Расширяемость везде сразу — это тот же overengineering, что и нарушение SOLID без причины.

### ↳ Углубление

**Интервьюер:** SRP применяется только к классам?

**Кандидат:**

Нет, не только. SRP сформулирован Робертом Мартином как «у модуля должна быть одна причина для изменения», и слово «модуль» здесь важно: классы — лишь одна из возможных единиц. Принцип применим на разных уровнях, а суть везде одна: группируй то, что меняется по одной причине, и разделяй то, что меняется по разным.

## Где SRP работает за пределами класса

**Функции и методы.** Метод, который парсит запрос, считает налог и пишет в БД, нарушает SRP так же, как класс с тремя обязанностями. Признак тот же: в одной функции смешаны уровни абстракции.

```php
// Одна функция делает три вещи разного характера
function importPrices(string $path): void
{
    $rows = array_map('str_getcsv', file($path));    // чтение формата
    foreach ($rows as [$sku, $price]) {
        $prices[$sku] = (int) round((float) $price * 100); // бизнес-правило
        $db->query("UPDATE products SET price = ...");     // хранение
    }
}
```

**Модули и пространства имён.** Директория `Billing` содержит и расчёт налогов, и отправку писем, и HTTP-контроллеры. Изменение формата письма и правил НДС попадают в один модуль, и связность низкая. Разделение по причинам изменения (bounded contexts в DDD) здесь полезнее, чем разделение по типам файлов.

**Микросервисы и сервисы.** Сервис, который одновременно владеет каталогом, ценами и отчётностью, меняется по трём независимым причинам и выкатывается вместе. Это тот же SRP, только в масштабе системы.

**Слои архитектуры.** Контроллер отвечает за HTTP, сценарий приложения за оркестровку, домен за правила, репозиторий за хранение. Смешение этих ролей в одном месте и есть нарушение SRP на уровне архитектуры.

**Конфигурация и миграции.** Файл `services.yaml`, в котором описаны и бизнес-зависимости, и параметры окружения, тоже может смешивать причины изменения: одно меняется при деплое, другое при развитии продукта.

## Критерий

Во всех случаях проверка одна: назови, кто и почему может попросить изменить этот элемент. Если таких причин несколько и они независимы, элемент стоит разделить. Если причина одна, даже длинный файл или модуль нормален.

Важная оговорка: SRP на уровне модулей и сервисов не означает обязательного дробления на микросервисы. Часто достаточно логических границ внутри одного приложения и правил о том, какой код от какого может зависеть.

> ⏱ 15.2s · prompt 8317 tok · output 3451 tok · кэш 5 644+2 669 tok · $0.0021

---

## Q05 · Symfony

**Интервьюер:** Объясни, как устроен контейнер внедрения зависимостей в Symfony: что происходит при компиляции контейнера, как работает autowiring и что происходит в момент, когда сервис запрошен впервые.

**Кандидат:**

Контейнер Symfony работает в два этапа: сначала он компилируется (в основном в dev-режиме при изменении конфигурации, в prod — при `cache:clear` или `cache:warmup`), затем во время выполнения отдаёт готовые сервисы. Ниже оба этапа и поведение при первом запросе сервиса.

## 1. Компиляция контейнера

Компиляция выполняется `ContainerBuilder` и набором compiler passes. Упрощённо:

**Загрузка конфигурации.** Symfony читает `services.yaml`, атрибуты классов (`#[AsEventListener]`, `#[Autowire]`, `#[AutoconfigureTag]`), расширения бандлов (`Extension::load()`), переменные окружения и параметры. Результатом становится набор определений `Definition` с классом, аргументами, тегами, вызовами методов и областью видимости.

**Autoconfiguration.** Если класс реализует интерфейс, зарегистрированный как «автоконфигурируемый» (например, `EventSubscriberInterface`, `Command`, `ServiceSubscriberInterface`), контейнер сам добавляет нужные теги.

**Autowiring.** На этом этапе для аргументов без явного значения вычисляются зависимости по типам. Подробнее ниже.

**Compiler passes.** Цепочка проходов преобразует граф определений. Примеры: `ResolveInvalidReferencesPass` проверяет ссылки, `RemovePrivateAliasesPass` и `RemoveUnusedDefinitionsPass` удаляют лишнее, `RegisterEventListenersAndSubscribersPass` собирает слушателей, `ResolveTaggedIteratorArgumentPass` собирает тегированные коллекции. Проходы могут менять граф, добавлять определения, вычищать неиспользуемые сервисы.

**Проверка и дамп.** Контейнер проверяет циклы, отсутствующие сервисы и несоответствие типов, затем генерирует PHP-класс (обычно `var/cache/.../App_KernelDevDebugContainer.php`) с методами для каждого публичного или используемого сервиса. Из-за этого ошибки конфигурации обычно всплывают не на первом запросе, а уже при сборке контейнера.

## 2. Как работает autowiring

Autowiring сопоставляет параметр конструктора с сервисом по типу (FQCN интерфейса или класса). Алгоритм примерно такой:

1. Для параметра с типом `Foo` ищется сервис с id `Foo`. Если есть алиас от интерфейса к реализации, используется он.
2. Если класс один и реализует интерфейс, Symfony может связать интерфейс с ним автоматически (при включённом `autowire` и отсутствии конфликта).
3. Если подходящих кандидатов несколько, а алиаса нет, контейнер выдаёт ошибку неоднозначности. Это хорошо: угадывать не нужно.
4. Скалярные параметры (`string $dsn`, `int $limit`) не автоподключаются. Они берутся из `%parameters%`, через `#[Autowire(param: 'app.limit')]` или через `bind` в `services.yaml`.
5. Опциональные параметры со значением по умолчанию используют дефолт, если сервиса нет.

```yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\:
        resource: '../src/'

    App\Billing\Domain\PaymentGateway:
        alias: App\Billing\Infrastructure\HttpPaymentGateway

    App\Billing\Infrastructure\HttpPaymentGateway:
        arguments:
            $apiKey: '%env(PAYMENT_API_KEY)%'
```

`bind` задаёт значение для всех сервисов с параметром указанного имени, например `$projectDir` или `$apiKey` сразу для всего приложения.

## 3. Что происходит при первом запросе сервиса

В prod-окружении сервисы создаются лениво. В compiled-контейнере для каждого сервиса есть метод вида `getXxxService()`, а публичный вход — `get($id)`.

Когда код вызывает `$container->get(Foo::class)` (или внедряется через конструктор другого сервиса, что обычно и происходит), последовательность такая:

1. **Проверка кэша экземпляров.** Если сервис `shared` (по умолчанию), контейнер смотрит, не создан ли он раньше. Если да, возвращает тот же объект, это и есть singleton-поведение в рамках запроса.
2. **Защита от циклов.** Контейнер помечает сервис как «в процессе создания». Если во время создания он снова запрашивается, это цикл, и контейнер бросает исключение. Циклы разрешаются через setter-инъекцию, `LazyProxy` или отложенный `ServiceLocator`.
3. **Рекурсивное создание зависимостей.** Для каждого аргумента конструктора контейнер вызывает соответствующий `getXxxService()`, и весь граф поднимается в глубину. Ленивые зависимости (`lazy: true` или `ghost`/`proxy`-объекты) вместо реального объекта получают прокси, который создаётся при первом обращении к методу.
4. **Создание объекта.** Вызывается конструктор с уже готовыми аргументами. Скалярные значения подставляются из параметров, уже вычисленные как `%env(...)%`.
5. **Вызовы настройки.** Выполняются `calls` (setter-инъекция), `configurator`, затем `tag`-зависимые действия: например, сервис регистрируется как слушатель.
6. **Сохранение и возврат.** Если сервис `shared`, экземпляр кладётся в кэш, затем возвращается вызывающему коду.

Практические следствия:

- Конструктор не должен делать тяжёлой работы: он выполняется при первом обращении к сервису, даже если метод не будет вызван. Для тяжёлых зависимостей используют `lazy`.
- Сервисы без состояния (как обработчики) безопаснее делать `shared`, а сервисы с состоянием на один запрос лучше сбрасывать (`ResetInterface`), особенно в long-running процессах (Messenger workers, RoadRunner, FrankenPHP).
- Внедрять контейнер в сервисы (service locator) — антипаттерн. Нужен конкретный сервис через конструктор, а не `$container->get(...)` внутри бизнес-логики.
- Ошибки вроде «circular reference» или «cannot autowire» видны уже при компиляции или при первом обращении, поэтому полезно регулярно запускать `bin/console debug:container` и `lint:container`.

### ↳ Углубление

**Интервьюер:** Как разруливаешь циклические зависимости между сервисами?

**Кандидат:**

Циклическая зависимость возникает, когда сервис A требует B в конструкторе, а B требует A. Контейнер не может построить такой граф: чтобы создать A, нужен B, а чтобы создать B, нужен A. Symfony обнаруживает это при сборке или при первом обращении и выбрасывает `ContainerException` с указанием цепочки.

Первое и главное правило: **цикл обычно означает, что граница между ответственностями проведена неверно**. Поэтому сначала я пытаюсь устранить сам цикл, и только потом применяю технические обходные пути.

## 1. Вынести общую логику в третий сервис

Чаще всего два сервиса зависят друг от друга, потому что оба нужны для одной и той же операции. Общую часть выношу наружу.

```php
// Было: OrderService <-> InventoryService
// OrderService нужен InventoryService для резерва,
// InventoryService нужен OrderService для проверки статуса заказа.

// Стало: оба зависят от третьего, цикла нет
final readonly class StockReservation
{
    public function __construct(private EntityManagerInterface $em) {}

    public function reserve(Order $order): void { /* ... */ }
}

final readonly class OrderService
{
    public function __construct(private StockReservation $reservation) {}
}

final readonly class InventoryService
{
    public function __construct(private StockReservation $reservation) {}
}
```

## 2. Развернуть направление через событие

Если один сервис лишь сообщает о факте, а другой на него реагирует, зависимость должна идти в одну сторону. Тогда первый публикует событие, второй его слушает.

```php
final readonly class OrderPaid
{
    public function __construct(public int $orderId) {}
}

final readonly class OrderService
{
    public function __construct(private EventDispatcherInterface $events) {}

    public function markPaid(Order $order): void
    {
        // ... смена статуса
        $this->events->dispatch(new OrderPaid($order->getId()));
    }
}

#[AsEventListener]
final readonly class InventoryReleaseListener
{
    public function __construct(private InventoryService $inventory) {}

    public function __invoke(OrderPaid $event): void { /* ... */ }
}
```

Теперь `OrderService` не знает о `InventoryService`. Цикл исчезает, а связь становится явной через контракт события.

## 3. Инверсия через интерфейс

Иногда одному сервису нужен узкий контракт от другого. Тогда выделяю интерфейс, принадлежащий потребителю, и реализация зависит от него, а не наоборот.

```php
// Интерфейс живёт рядом с потребителем
interface OrderStatusProvider
{
    public function statusOf(int $orderId): OrderStatus;
}

final readonly class InventoryService
{
    public function __construct(private OrderStatusProvider $orders) {}
}
```

## 4. Setter-инъекция (крайний случай)

Если цикл действительно неустраним (например, два сервиса на уровне инфраструктуры, которые по природе ссылаются друг на друга), один из сервисов получает зависимость через setter, а не через конструктор. Конструктор при этом остаётся без цикла.

```php
final class EventLogger
{
    private ?MetricsCollector $metrics = null;

    public function setMetrics(MetricsCollector $metrics): void
    {
        $this->metrics = $metrics;
    }
}
```

Минус очевиден: объект может существовать в неполном состоянии, поэтому `$metrics` приходится проверять на `null`. Используют только когда иначе никак.

## 5. Ленивая зависимость (`lazy: true`)

Symfony может передать вместо реального сервиса прокси, который создаст его при первом вызове метода. Цикл разрывается, потому что при конструировании реального объекта не требуется.

```php
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class ReportBuilder
{
    public function __construct(
        #[Autowire(lazy: true)] private MailService $mail,
    ) {}
}
```

Здесь важно помнить, что прокси скрывает реальную зависимость, и разработчик может не заметить, что цикл всё ещё есть. Поэтому лучше использовать это как временное решение с пометкой.

## 6. ServiceLocator или `#[Autowire(service: ...)]` с отложенным получением

Для редких случаев можно внедрить `ContainerInterface` или `ServiceLocator` и получить сервис в момент использования. Это ослабляет контракт и делает зависимость невидимой, поэтому это последнее средство, а не решение по умолчанию.

## Как я выбираю

1. Есть ли общая логика? Выношу её (п. 1).
2. Один сервис только сообщает о факте? Перевожу на события (п. 2).
3. Нужен узкий контракт? Вводлю интерфейс на стороне потребителя (п. 3).
4. Иначе: setter или lazy как временная мера, с фиксацией в коде и задачей на рефакторинг.

Цикл в графе сервисов почти всегда сигнал о том, что два класса знают слишком много друг о друге. Обход через setter или lazy снимает симптом, но не причину.

> ⏱ 13.5s · prompt 14955 tok · output 2328 tok · кэш 13 961+990 tok · $0.0014

---

## Q06 · Symfony

**Интервьюер:** Представь: после успешной регистрации пользователя нужно отправить письмо и обновить поисковый индекс, и это не должно замедлять ответ пользователю. Как организуешь это в Symfony-приложении? Опиши возможные варианты и их trade-offs.

**Кандидат:**

Задача сводится к одному: отделить ответ пользователю от побочных эффектов, которые могут быть медленными или ненадёжными. Есть несколько вариантов, и они различаются по надёжности, сложности и тому, где выполняется работа.

## Вариант 1. Синхронный обработчик события (Symfony EventDispatcher)

Контроллер публикует `UserRegistered`, слушатели выполняются в том же HTTP-запросе.

```php
final readonly class UserRegistered
{
    public function __construct(public int $userId, public string $email) {}
}

#[AsEventListener]
final readonly class SendWelcomeEmail
{
    public function __construct(private MailerInterface $mailer) {}

    public function __invoke(UserRegistered $event): void { /* ... */ }
}
```

Плюсы: просто, без инфраструктуры, легко тестировать.
Минусы: ответ ждёт письмо и индексацию. Если поисковый движок недоступен, регистрация может упасть с 500, хотя пользователь уже создан в БД. Это главный недостаток.

Подходит только для быстрых и локальных побочных эффектов.

## Вариант 2. Отложенная отправка через Messenger (асинхронно через транспорт)

Слушатель отправляет сообщение в очередь, а воркер обрабатывает его позже. Это основной вариант для Symfony.

```php
final readonly class IndexUser
{
    public function __construct(public int $userId) {}
}

#[AsMessageHandler]
final readonly class IndexUserHandler
{
    public function __construct(private SearchIndex $index, private UserRepository $users) {}

    public function __invoke(IndexUser $message): void
    {
        $user = $this->users->find($message->userId);
        if ($user === null) {
            return; // пользователь удалён до обработки
        }
        $this->index->upsert($user);
    }
}
```

В `messenger.yaml` задаются транспорт (Doctrine, Redis, AMQP) и маршрутизация `routing: { App\Message\IndexUser: async }`. Письмо отправляется аналогично отдельным сообщением `SendWelcomeEmail`.

Важный момент: передаём в сообщение **идентификатор**, а не сущность Doctrine. Сущность к моменту обработки может быть уже detached или устаревшей.

Плюсы: ответ быстрый, повторы и dead letter queue из коробки (`retry_strategy`, `failure_transport`), обработчики независимы друг от друга.
Минусы: нужен воркер (`messenger:consume`, supervisor или systemd), появляется eventual consistency, сообщения могут обрабатываться дважды, поэтому обработчики должны быть идемпотентными.

## Вариант 3. Транзакционная outbox

Если важно, чтобы событие не потерялось, даже если очередь недоступна в момент коммита, событие записывается в таблицу `outbox` в той же транзакции, что и пользователь. Отдельный процесс читает таблицу и публикует сообщения.

Плюсы: строгая гарантия «пользователь создан, значит событие будет доставлено». Минусы: дополнительная таблица, поллер или CDC (например, Debezium), больше движущихся частей. Оправдано, если индексация или письма критичны для бизнеса.

## Вариант 4. `kernel.terminate` или `fastcgi_finish_request`

Symfony вызывает `kernel.terminate` после отправки ответа клиенту, и работа выполняется в том же процессе.

```php
#[AsEventListener(event: KernelEvents::TERMINATE)]
final readonly class IndexAfterResponse { /* ... */ }
```

Плюсы: без очереди и воркеров, ответ уходит сразу.
Минусы: процесс PHP занят, при падении работа теряется без повторов, под нагрузкой ухудшает пропускную способность PHP-FPM. Подходит для небольших задач и как временное решение, но не для критичных операций.

## Вариант 5. Внешний сервис или cron

Индексацию можно вынести в фоновую задачу по расписанию, которая ищет незаиндексированных пользователей (например, по флагу `indexed_at`). Это простой и устойчивый способ, но с задержкой.

## Что я выберу

Для большинства случаев: **Messenger с транспортом на Redis или Doctrine, с отдельными сообщениями на письмо и индексацию, с передачей ID, с идемпотентными обработчиками и `failure_transport`**. Если потеря события недопустима, добавляю outbox. Синхронные слушатели оставляю для того, что нужно выполнить до ответа (например, создание связанных записей в той же транзакции).

Отдельно важно: отправку в очередь тоже стоит делать после успешного коммита транзакции (Doctrine, `kernel.request`/`flush`), иначе воркер может получить ID пользователя, которого ещё нет в БД.

> ⏱ 10.2s · prompt 19215 tok · output 1710 tok · кэш 17 302+1 909 tok · $0.0013

---

## Q07 · Symfony

**Интервьюер:** Как в Symfony устроен контроль доступа: аутентификация, роли, voters? В каких случаях выбираешь voter, а в каких достаточно ролей?

**Кандидат:**

Symfony разделяет три вопроса, и их полезно не смешивать: **кто пользователь** (аутентификация), **какие у него общие полномочия** (роли) и **может ли он сделать конкретное действие с конкретным объектом** (voters). Всё это работает через `Security`-компонент и firewall.

## 1. Аутентификация

Firewall в `security.yaml` задаёт, как распознать пользователя: форма входа, API-токен, JWT, OIDC и т. д. Аутентификатор создаёт токен (`TokenInterface`) и кладёт его в хранилище. Пользователь представлен классом, реализующим `UserInterface`, а провайдер (`entity` или кастомный) загружает его.

```yaml
security:
    providers:
        app_users:
            entity: { class: App\Entity\User, property: email }
    firewalls:
        api:
            pattern: ^/api
            stateless: true
            custom_authenticator: App\Security\ApiTokenAuthenticator
        main:
            lazy: true
            form_login: { login_path: login, check_path: login }
```

Аутентификация отвечает только на вопрос «кто». Она не решает, что этому пользователю разрешено.

## 2. Роли

Роли — строки вида `ROLE_ADMIN`, которые пользователь получает из `getRoles()`. Иерархия задаёт наследование:

```yaml
security:
    role_hierarchy:
        ROLE_ADMIN: [ROLE_EDITOR, ROLE_USER]
```

Проверка выглядит так: `#[IsGranted('ROLE_ADMIN')]` на контроллере или `$this->isGranted('ROLE_ADMIN')`. Роли хороши для **грубых, глобальных** разрешений: доступ в админку, раздел для модераторов, функции, включённые для тарифа.

Их слабость в том, что роль не знает об объекте. `ROLE_EDITOR` говорит, что пользователь редактор вообще, но не то, может ли он редактировать статью №42, которую создал другой автор.

## 3. Voters

Voter отвечает на вопрос «может ли субъект выполнить атрибут над объектом». Он принимает атрибут (строку, например `EDIT`), объект и токен, и возвращает `ACCESS_GRANTED`, `ACCESS_DENIED` или `ACCESS_ABSTAIN`.

```php
final class ArticleVoter extends Voter
{
    public const EDIT = 'ARTICLE_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::EDIT && $subject instanceof Article;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Article $subject */
        return $subject->getAuthor() === $user
            || $this->isGranted('ROLE_ADMIN', $token); // см. ниже
    }
}
```

Использование:

```php
#[IsGranted('ARTICLE_EDIT', 'article')]
public function edit(Article $article): Response { /* ... */ }

// или в сервисе
$this->security->isGranted('ARTICLE_EDIT', $article);
```

Важно: в `Voter` `$this->isGranted` внутри `voteOnAttribute` — это не метод контроллера. Для проверки роли внутри voter'а нужен `AccessDecisionManagerInterface` или внедрение `Security`, а не голый `isGranted` из контроллера. В примере выше это упрощено; в коде лучше внедрить `Security` и вызвать `$this->security->isGranted('ROLE_ADMIN')`, а voter проверять только объектную логику.

## Стратегии принятия решения

`access_decision_manager` в `security.yaml` задаёт, как сводятся голоса: `affirmative` (достаточно одного `GRANTED`, по умолчанию), `consensus` (большинство), `unanimous` (все). Для большинства приложений `affirmative` подходит.

## Когда что выбирать

**Достаточно ролей**, если:
- решение не зависит от конкретного объекта («админ может открыть `/admin`»);
- правило статично и понятно, а ролей немного;
- это feature flag по тарифу или типу аккаунта, который не привязан к записи.

**Нужен voter**, если:
- решение зависит от связи «пользователь — объект» (владелец, участник команды, арендатор);
- нужна та же логика в контроллере, шаблонах Twig, сервисе и консольной команде: один атрибут, один источник правды;
- правило сложное и его нужно тестировать изолированно (voter легко юнит-тестировать);
- правил много, и `if` в контроллерах начинает дублироваться.

**Типичная ошибка**: создавать `ROLE_ARTICLE_EDITOR_42` для каждой записи или проверять `$article->getAuthor() === $user` вручную в каждом контроллере. Первое не масштабируется, второе дублирует правило и легко забыть.

Практически я комбинирую оба подхода: роли для глобальных разрешений по всему приложению, voters для правил, привязанных к объектам. Также стоит помнить, что `#[IsGranted]` и voter'ы срабатывают в контроллере, но в сервисах доступ нужно проверять явно, иначе бизнес-логика может обойти проверку, вызванную через HTTP.

> ⏱ 11.1s · prompt 20982 tok · output 1925 tok · кэш 19 211+1 767 tok · $0.0014

---

## Q08 · Doctrine

**Интервьюер:** Как в Doctrine хранить Value Object, если домен требует от него неизменяемости и всегда валидного состояния? Какие есть способы маппинга, их плюсы, минусы и подводные камни?

**Кандидат:**

Value Object в домене должен быть неизменяемым и всегда валидным, а ORM по своей природе стремится менять состояние сущности и создавать объекты без вызова конструктора. Поэтому маппинг VO — это компромисс между доменной чистотой и возможностями Doctrine. Ниже основные способы.

## Что нужно Doctrine

Doctrine при гидрации создаёт объект через `ReflectionClass::newInstanceWithoutConstructor()` и заполняет свойства напрямую через рефлексию. Значит:

- конструктор VO **не вызывается** при загрузке из БД, и инварианты в нём не проверяются;
- `readonly`-свойства PHP 8.1+ Doctrine может заполнить, но только если класс и свойства совместимы с её гидрацией (в современных версиях это работает);
- для записи в БД нужны геттеры или доступ к свойствам, что конфликтует с идеей инкапсуляции.

## Способ 1. Embeddable

Самый естественный вариант: VO маппится как `#[ORM\Embeddable]`, его поля разворачиваются в колонки владельца.

```php
#[ORM\Embeddable]
final readonly class Money
{
    #[ORM\Column(type: 'integer')]
    public int $amount;

    #[ORM\Column(length: 3)]
    public string $currency;

    public function __construct(int $amount, string $currency)
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Negative amount');
        }
        $this->amount = $amount;
        $this->currency = $currency;
    }
}

#[ORM\Entity]
class Order
{
    #[ORM\Embedded(class: Money::class, columnPrefix: 'total_')]
    private Money $total;
}
```

Плюсы: типобезопасность, одна таблица, запросы по полям (`o.total.amount`) через DQL. Минусы: при `null` в полях Doctrine может создать `null` вместо объекта, nullable-embeddable требует внимания; сложные VO с коллекциями не поддерживаются; `readonly` работает, но при гидрации полезно проверить версию Doctrine и тесты на загрузку.

Подводный камень: инвариант конструктора не защищает данные, которые попали в БД в обход домена (прямой SQL, старые миграции). Поэтому проверки дублируются в миграциях.

## Способ 2. Custom Type

Для VO, который хранится одним значением (email, ULID, телефон, enum-подобный код), пишется `DBAL Type`.

```php
final class EmailType extends \Doctrine\DBAL\Types\StringType
{
    public const NAME = 'email';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Email
    {
        return $value === null ? null : new Email($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value?->value;
    }

    public function getName(): string { return self::NAME; }
}
```

Регистрация в `doctrine.yaml` через `types:`, затем `#[ORM\Column(type: 'email')]`.

Плюсы: VO полностью инкапсулирован, в маппинге одна строка, гидрация идёт через конструктор, инварианты выполняются. Минусы: сложнее запросы (в DQL сравниваем с примитивом, а не с объектом), нужна регистрация типа, в `convertToPHPValue` при невалидных данных из БД будет исключение во время гидрации.

Подводный камень: `convertToPHPValue` вызывается часто, при большой выборке создание объектов становится заметным.

## Способ 3. Маппинг через `Embedded` с приватными полями и фабрикой

Вариант 1 с приватными свойствами и `#[ORM\Embedded]`. Doctrine заполнит поля рефлексией, поэтому инвариант не восстанавливается при загрузке. Используется редко, когда нужна скрытость полей без геттеров.

## Способ 4. Отдельная сущность

VO, у которого есть собственная идентичность и жизненный цикл (адрес с историей, версия документа), на самом деле не VO. Его маппят как `Entity` с `OneToOne`. Это честнее, если в домене появилась идентичность.

## Способ 5. Сериализация в JSON или массив

`#[ORM\Column(type: 'json')]` с ручным преобразованием в VO через `fromArray`. Просто, но теряется схема, нельзя эффективно искать по полям, и ошибки формата всплывают в рантайме. Оправдано для неструктурированных данных (настройки, метаданные).

## Общие подводные камни

- **Гидрация обходит конструктор** для embeddable, поэтому инварианты в БД не гарантированы. Держу тесты на загрузку и миграции с проверками.
- **Изменяемость в запросах.** `UPDATE ... SET total = ...` через DQL обходит объект. Для VO обычно заменяю целиком, а не меняю поле.
- **Сравнение.** Doctrine при изменении сущности сравнивает embeddable по значениям, но для собственных `Type` сравнение идёт через `convertToDatabaseValue`. Для VO нужен корректный `equals`.
- **Null и пустые значения.** Nullable-embeddable и `null` в колонке должны совпадать с моделью домена; иначе появляются объекты с `null`-полями или исключения.
- **Доменные события и копирование.** Поскольку VO неизменяем, при «изменении» создаётся новый экземпляр, и его нужно присвоить сущности через метод агрегата, а не через сеттер.
- **Индексы и поиск.** Embeddable удобен для поиска по полям, custom type — для поиска по одному значению (например, `WHERE email = :email` с нормализацией в приложении).

## Что выбираю

- Составной VO из нескольких полей (деньги, диапазон дат, адрес): **Embeddable**.
- VO из одного значения с инвариантами (email, ULID, телефон): **Custom Type**.
- Идентичность и жизненный цикл: **Entity**.
- Неструктурированные данные: **JSON** с валидацией на входе.

Главное правило: инварианты должны выполняться и при создании, и при восстановлении из БД. Doctrine этого сама не гарантирует, поэтому custom type с конструктором обычно надёжнее embeddable.

> ⏱ 13.2s · prompt 22977 tok · output 2234 tok · кэш 20 978+1 995 tok · $0.0016

---

## Q09 · Doctrine

**Интервьюер:** Доменный агрегат состоит из корня и коллекции дочерних сущностей. Как организовать его хранение через Doctrine: маппинг коллекций, границы транзакции, кто и когда вызывает flush? Разбери на примере по твоему выбору.

**Кандидат:**

Возьму пример заказа: `Order` — корень агрегата, `OrderLine` — дочерние позиции. Правило агрегата: изменять строки можно только через заказ, и заказ загружается и сохраняется целиком, как единица консистентности.

## Маппинг коллекции

Коллекция приватная, доступ только через методы корня. Внешний код не должен получать возможность добавить строку в обход инвариантов.

```php
#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid')]
    private Ulid $id;

    #[ORM\Column(length: 20, enumType: OrderStatus::class)]
    private OrderStatus $status;

    /** @var Collection<int, OrderLine> */
    #[ORM\OneToMany(
        targetEntity: OrderLine::class,
        mappedBy: 'order',
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
    )]
    private Collection $lines;

    public function __construct(Ulid $id)
    {
        $this->id = $id;
        $this->status = OrderStatus::New;
        $this->lines = new ArrayCollection();
    }

    public function addLine(Product $product, int $quantity): void
    {
        if ($this->status !== OrderStatus::New) {
            throw new DomainException('Заказ нельзя менять в статусе ' . $this->status->value);
        }
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Количество должно быть положительным');
        }
        $this->lines->add(new OrderLine($this, $product, $quantity));
    }

    public function removeLine(OrderLine $line): void
    {
        $this->lines->removeElement($line);
    }

    /** @return list<OrderLine> */
    public function lines(): array
    {
        return $this->lines->toArray();
    }
}

#[ORM\Entity]
class OrderLine
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'lines')]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    #[ORM\Id]
    #[ORM\Column(type: 'ulid')]
    private Ulid $id;

    #[ORM\Column(type: 'integer')]
    private int $quantity;

    public function __construct(Order $order, Product $product, int $quantity)
    {
        $this->order = $order;
        $this->id = new Ulid();
        $this->quantity = $quantity;
    }
}
```

Ключевые моменты маппинга:

- `orphanRemoval: true` означает, что строка, удалённая из коллекции, удаляется из БД. Без него она просто получит `order_id = NULL`, если колонка nullable, или упадёт при flush.
- `cascade: ['persist']` позволяет сохранять строки вместе с заказом, поэтому репозиторий не вызывает `persist` для каждой строки.
- Двусторонняя связь: владелец — `OrderLine::$order`, обратная сторона — `Order::$lines`. Поле `mappedBy` и `inversedBy` должны совпадать.
- Составной ключ (`order` + `id`) выбран для примера; в реальном проекте проще взять суррогатный `id` у строки и уникальный индекс по `(order_id, product_id)`, если это правило домена.

Важно не отдавать `Collection` наружу: `lines()` возвращает копию массива, а не саму коллекцию. Иначе `$order->lines()->add(...)` обойдёт инварианты.

## Загрузка

По умолчанию `lines` ленивая, это хорошо для агрегата: при загрузке заказа строки подгружаются, когда к ним обращаются. Для типичного сценария «открыть заказ и показать позиции» полезно загрузить всё одним запросом:

```php
$order = $this->em->createQueryBuilder()
    ->select('o', 'l')
    ->from(Order::class, 'o')
    ->leftJoin('o.lines', 'l')
    ->where('o.id = :id')
    ->setParameter('id', $id)
    ->getQuery()
    ->getOneOrNullResult();
```

Для агрегата загружать его целиком обычно правильно. Для списков и отчётов, где нужны только агрегированные данные, лучше отдельные DTO-запросы, а не гидрация агрегата.

## Границы транзакции

Транзакция охватывает одну операцию над одним агрегатом. Все изменения корня и его строк сохраняются атомарно. Правило: **одна команда — один агрегат — одна транзакция**. Если сценарий затрагивает несколько агрегатов, между ними используются события и отдельные транзакции (eventual consistency), а не один большой `flush`.

## Кто и когда вызывает flush

- **Flush вызывает слой приложения** (application service или command handler), а не домен и не репозиторий. Домен меняет состояние в памяти, не зная о БД.
- **Репозиторий** отвечает за загрузку и добавление корня: `add(Order $order)` делает `persist`, но не `flush`, `find` возвращает агрегат целиком.
- **Один flush на команду.** Doctrine накапливает изменения в Unit of Work и отправляет их одним пакетом внутри транзакции, которую обычно открывает middleware или явная обёртка:

```php
final readonly class AddLineHandler
{
    public function __construct(
        private OrderRepository $orders,
        private EntityManagerInterface $em,
    ) {}

    public function __invoke(AddLineCommand $cmd): void
    {
        $order = $this->orders->get($cmd->orderId);
        $order->addLine($cmd->product, $cmd->quantity);

        $this->em->wrapInTransaction(function () use ($order): void {
            $this->em->flush();
        });
    }
}
```

`wrapInTransaction` (Doctrine ORM 2.x/3.x) сам откатывает при исключении. Если сценарий затрагивает несколько агрегатов, flush один, а события о смене состояния публикуются после коммита.

## Подводные камни

- **Flush внутри домена или в `addLine`** делает объект зависимым от инфраструктуры. Не допускаю.
- **Несколько flush** в одной команде ломают атомарность: часть изменений зафиксирована, часть нет.
- **Изменение строки через `OrderLine` напрямую**, минуя корень, нарушает инварианты агрегата. Если строка публична, защищаю её приватными сеттерами и методами корня.
- **Оптимистическая блокировка.** При параллельных изменениях заказа добавляю `#[ORM\Version]` в корень, чтобы второй flush падал с `OptimisticLockException`, а не перезаписывал чужие изменения.
- **`clear()` и ленивые прокси.** После `clear()` все ранее загруженные ссылки становятся устаревшими; в длинных процессах (воркеры Messenger) очищаю EntityManager между сообщениями.
- **N+1 при обходе строк** в шаблонах или сериализации решается `JOIN FETCH`, как показано выше.

Итог: агрегат загружается целиком, меняется только через корень, сохраняется одним flush в одной транзакции, а flush вызывает слой приложения.

> ⏱ 13.0s · prompt 25300 tok · output 2549 tok · кэш 22 973+2 323 tok · $0.0018

---

## Q10 · Doctrine

**Интервьюер:** Страница приложения тормозит, есть подозрение на проблему N+1 запросов в Doctrine. Как ты диагностируешь проблему и какие способы решения знаешь? Опиши trade-offs каждого способа.

**Кандидат:**

Сначала подтверждаю диагноз, потом устраняю причину. Подозрение на N+1 нельзя считать фактом, пока его не увидели в профайлере.

## Диагностика

**1. Symfony Profiler и Doctrine-панель.** В веб-профайлере во вкладке Doctrine видно общее число запросов и время. Если на одну страницу приходятся десятки или сотни однотипных `SELECT ... WHERE id = ?` с разными параметрами, это почти всегда N+1. Для сравнения смотрю время запроса к странице до и после правок.

**2. Лог запросов в dev.** Включаю логирование SQL (`doctrine.dbal.logging` / middleware) и смотрю, повторяется ли один шаблон запроса с разными значениями.

**3. Команда и тесты.** Для воспроизводимости пишу тест, который считает запросы: через `DoctrineTestHelper`, middleware или `Doctrine\DBAL\Logging\Middleware` с счётчиком. Тест фиксирует, что страница делает не больше, чем N константных запросов, и защищает от регрессии.

**4. Тайминги и EXPLAIN.** Если запросов мало, но страница медленная, проблема может быть не в N+1, а в плане выполнения. Тогда смотрю `EXPLAIN ANALYZE` в PostgreSQL и индексы.

**5. Blackfire / Xdebug profiler.** Для поиска горячих точек в PHP, например гидрации тысяч объектов.

## Типичный пример

```php
// Контроллер
$orders = $this->orderRepo->findBy(['status' => 'paid']); // 1 запрос

// Шаблон Twig
{% for order in orders %}
    {{ order.customer.name }}  {# отдельный запрос на каждого клиента #}
    {% for line in order.lines %}  {# ещё один запрос на каждый заказ #}
        {{ line.product.title }}   {# и ещё один на каждую строку #}
    {% endfor %}
{% endfor %}
```

Сотни заказов дают сотни и тысячи запросов.

## Способы решения

### 1. Fetch join в DQL / QueryBuilder

```php
$orders = $this->em->createQueryBuilder()
    ->select('o', 'c', 'l', 'p')
    ->from(Order::class, 'o')
    ->join('o.customer', 'c')
    ->leftJoin('o.lines', 'l')
    ->leftJoin('l.product', 'p')
    ->where('o.status = :s')->setParameter('s', 'paid')
    ->getQuery()
    ->getResult();
```

- **Плюсы:** один запрос на нужные связи, полный контроль, понятный SQL.
- **Минусы:** декартово произведение строк при нескольких коллекциях (`lines` и `payments` одновременно даст мультипликацию данных). Для пагинации с fetch join `Paginator` с `fetchJoinCollection: true` делает подзапрос, это дороже. Избыточные поля при больших графах.

### 2. Batch-загрузка (extra lazy / batch fetching)

Doctrine при обращении к одной ленивой связи догружает связанные сущности пачкой через `IN (...)`. Включается `fetch: 'EXTRA_LAZY'` для коллекций (для `count()` без загрузки) или настройкой batch size у сущности (`#[ORM\BatchSize(size: 50)]` в ORM 2.x/3.x).

- **Плюсы:** минимальные правки, код остаётся прежним, запросов не N, а N/batch.
- **Минусы:** всё равно больше одного запроса, поведение менее предсказуемо, `EXTRA_LAZY` не решает итерацию по всей коллекции.

### 3. Отдельный запрос с `IN` вручную

Загружаю заказы, собираю их ID, одним запросом получаю строки с `WHERE order_id IN (...)`, группирую в PHP.

- **Плюсы:** полный контроль, легко понять и протестировать.
- **Минусы:** больше кода, мапинг вручную, легко ошибиться при группировке.

### 4. DTO / скалярные запросы (проекции)

Если странице нужны только поля для отображения, а не объекты, выбираю DTO через `NEW` в DQL или `Result` через `select(...)` в DBAL.

```php
$rows = $this->em->createQuery(
    'SELECT NEW App\Dto\OrderRow(o.id, c.name, COUNT(l.id), SUM(l.quantity))
     FROM App\Entity\Order o
     JOIN o.customer c
     LEFT JOIN o.lines l
     WHERE o.status = :s
     GROUP BY o.id, c.name'
)->setParameter('s', 'paid')->getResult();
```

- **Плюсы:** минимум данных, быстро, без гидрации графа.
- **Минусы:** нарушается идея работы с агрегатом, код дублирует запросы для разных экранов, сложнее повторно использовать поведение сущности.

### 5. Кэш

Результат страницы или фрагмента кэшируется (HTTP-кэш, Symfony Cache, `#[Cache]`-аннотации для Twig). Полезно для редко меняющихся данных.

- **Плюсы:** самый быстрый ответ.
- **Минусы:** не исправляет запросы, устаревание, инвалидация сложна, первый запрос всё равно медленный.

## Что выбираю

1. Подтверждаю N+1 в профайлере и фиксирую счётчик запросов тестом.
2. Для экранов со списком и связями: fetch join, с осторожностью к коллекциям и пагинации.
3. Для отчётов и таблиц без поведения сущностей: DTO-проекции.
4. Batch size ставлю как страховку для остальных мест, где связи всё же ленивые.
5. Кэш добавляю последним, когда запросы уже оптимальны и данные действительно стабильны.

Главный принцип: сначала измерить, потом исправить, и каждый исправленный запрос закрепить тестом на количество обращений к БД.

> ⏱ 12.9s · prompt 27926 tok · output 2134 tok · кэш 25 296+2 626 tok · $0.0016

---

## Q11 · PostgreSQL

**Интервьюер:** Вся бизнес-валидация делается в коде приложения. Зачем тогда ограничения целостности на уровне базы данных в PostgreSQL? Какие средства для этого используешь и в каких случаях?

**Кандидат:**

Код приложения проверяет правила, но не гарантирует их. Между проверкой и записью есть гонки, в базу могут писать другие процессы, миграции, ручной SQL, а баги в коде или будущие изменения могут обойти проверку. Ограничения в БД — последняя линия обороны, которая работает независимо от того, как написан код. Поэтому я дублирую критичные инварианты в схеме, а не выбираю между кодом и базой.

## Почему одной проверки в коде мало

- **Гонки (race conditions).** Два запроса одновременно проверяют, что email свободен, и оба вставляют его. Только `UNIQUE` в базе гарантирует, что второй упадёт.
- **Несколько точек входа.** Приложение, консольные команды, воркеры Messenger, импорт, ручные правки через psql.
- **Эволюция кода.** Новый разработчик или новый метод может создать запись, минуя доменный объект.
- **Ошибки при гидрации и миграциях.** Данные, которые были валидны по старым правилам, остаются в таблице.

## Средства PostgreSQL и когда их использую

**1. `NOT NULL`.** Базовое правило: поле обязательно. Ставлю почти всегда, если в домене поле не может отсутствовать. Nullable-колонки делаю осознанно.

**2. `UNIQUE` и уникальные индексы.** Для идентичности и бизнес-ключей: email пользователя, номер заказа, внешний ключ в паре с другим полем.

```sql
CREATE UNIQUE INDEX uniq_users_email_lower ON users (lower(email));
```

Частичный уникальный индекс решает правило «уникально среди активных»:

```sql
CREATE UNIQUE INDEX uniq_active_coupon ON coupons (code) WHERE deleted_at IS NULL;
```

**3. `CHECK`.** Инварианты одной строки: диапазоны, допустимые значения, согласованность полей.

```sql
ALTER TABLE orders ADD CONSTRAINT chk_total_non_negative CHECK (total_cents >= 0);
ALTER TABLE orders ADD CONSTRAINT chk_paid_has_date
    CHECK (status <> 'paid' OR paid_at IS NOT NULL);
```

**4. Внешние ключи (`FOREIGN KEY`).** Ссылочная целостность. Выбираю `ON DELETE` осознанно: `RESTRICT` по умолчанию, `CASCADE` только для принадлежащих агрегату строк (строки заказа), `SET NULL` редко. В Doctrine это `#[ORM\JoinColumn(onDelete: 'CASCADE')]`, но важно, что Doctrine не знает о каскаде в БД, если не указать явно.

**5. Enum-типы и домены.** Для ограниченного набора значений: `CREATE TYPE` или `DOMAIN`. Для статусов чаще беру `CHECK (status IN (...))`, потому что enum в PostgreSQL неудобно менять миграциями.

```sql
CREATE DOMAIN positive_money AS integer CHECK (VALUE >= 0);
```

**6. Exclusion constraints.** Для непересечения диапазонов, например бронирования одного номера на перекрывающиеся даты. Это то, что `UNIQUE` выразить не может.

```sql
CREATE EXTENSION IF NOT EXISTS btree_gist;
ALTER TABLE reservations ADD CONSTRAINT no_overlap
    EXCLUDE USING gist (room_id WITH =, during WITH &&);
```

**7. Триггеры.** Для правил, которые зависят от нескольких таблиц или требуют вычислений, и для аудита. Использую редко: логика в триггерах скрыта от приложения, трудна в тестировании и миграциях. Предпочитаю `CHECK` и constraint triggers только там, где без них нельзя.

**8. Deferrable constraints.** `DEFERRABLE INITIALLY DEFERRED` откладывает проверку до коммита. Полезно для взаимных ссылок внутри одной транзакции.

## Что важно помнить

- Ограничения дают ошибку, а не пользовательское сообщение. Приложение должно перехватывать `UniqueConstraintViolationException` и `ForeignKeyConstraintViolationException` из DBAL и превращать их в доменные исключения. Не полагаться на текст сообщения, а проверять SQLSTATE (23505 для unique, 23503 для foreign key, 23514 для check).
- Проверка в коде всё равно нужна: она даёт понятные ошибки до запроса и корректный UX. База страхует, а не заменяет домен.
- Дублирование правил в двух местах требует дисциплины: изменение правила означает миграцию и в схеме, и в коде.
- Миграции с добавлением `CHECK` или `UNIQUE` на большой таблице блокируют запись. Использую `NOT VALID` с последующим `VALIDATE CONSTRAINT`, а для индексов `CREATE UNIQUE INDEX CONCURRENTLY`.

## Итог

Правила, которые должны выполняться всегда и при любом способе записи, (уникальность, ссылочная целостность, допустимые диапазоны) фиксирую в PostgreSQL. Сложные бизнес-сценарии, зависящие от контекста и состояния, остаются в коде домена, а база защищает от того, что код пропустил.

> ⏱ 11.7s · prompt 30132 tok · output 1904 tok · кэш 27 922+2 206 tok · $0.0015

---

## Q12 · PostgreSQL

**Интервьюер:** Запрос в PostgreSQL выполняется медленно. Опиши свой процесс диагностики и оптимизации: какие инструменты применяешь, что смотришь, какие типовые причины встречаются.

**Кандидат:**

Сначала измеряю, потом меняю. Оптимизация без данных о плане чаще ухудшает ситуацию, чем улучшает.

## 1. Воспроизвести и измерить

Беру точный SQL, который выполняет приложение (из профайлера Doctrine или из `log_min_duration_statement`), и запускаю его с реальными параметрами на копии данных, близкой по объёму к продакшену. Запрос на таблице в тысячу строк и в сто миллионов ведёт себя по-разному, поэтому пустая dev-база мало что показывает.

Включаю в PostgreSQL логирование медленных запросов:

```sql
ALTER SYSTEM SET log_min_duration_statement = '200ms';
SELECT pg_reload_conf();
```

Для постоянного анализа полезен `pg_stat_statements`: он показывает не один медленный запрос, а суммарное время по всем вызовам. Иногда запрос, который тормозит 50 мс, но выполняется тысячу раз в минуту, важнее одного «медленного».

## 2. Смотреть план

Основной инструмент: `EXPLAIN (ANALYZE, BUFFERS)`.

```sql
EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT o.id, o.total_cents
FROM orders o
WHERE o.customer_id = 42 AND o.status = 'paid'
ORDER BY o.created_at DESC
LIMIT 20;
```

Что смотрю в выводе:

- **Тип доступа**: `Seq Scan` на большой таблице с селективным фильтром подозрителен; `Index Scan` или `Index Only Scan` обычно лучше.
- **Расхождение оценок**: `rows=` (оценка планировщика) против `actual rows=`. Большая разница означает устаревшую статистику или коррелированные колонки. Лечится `ANALYZE`, а при корреляции — расширенной статистикой `CREATE STATISTICS`.
- **`Rows Removed by Filter`**: много отброшенных строк означает, что индекс не покрывает фильтр.
- **`Sort` с `external merge Disk`**: сортировка не влезла в `work_mem`, либо нужен индекс под `ORDER BY`.
- **`BUFFERS`**: `shared read` против `shared hit` показывает, сколько данных читается с диска.
- **Nested Loop** с большим числом итераций внутреннего узла: часто следствие неверной оценки и выбора не того соединения.
- **Время узла против общего**: `actual time` в PostgreSQL кумулятивно по дочерним узлам, поэтому смотрю на собственный вклад узла.

Визуализировать план удобно через explain.dalibo.com или pgMustard.

## 3. Типовые причины

**Нет подходящего индекса.** Самая частая. Индекс под `WHERE customer_id = ? AND status = ?` с порядком по `created_at` (составной индекс `(customer_id, status, created_at DESC)`) часто убирает и фильтр, и сортировку.

```sql
CREATE INDEX CONCURRENTLY idx_orders_customer_status_created
    ON orders (customer_id, status, created_at DESC);
```

**Частичный индекс** для часто встречаемого условия: `WHERE status = 'paid'` вместо индекса по всей таблице.

**Индекс есть, но не используется.** Причины: функция над колонкой (`WHERE lower(email) = ...` без функционального индекса), неявное приведение типов (`varchar` сравнивается с `int`), `LIKE '%текст'` без `pg_trgm`, низкая селективность, когда планировщик считает Seq Scan дешевле.

**Index Only Scan не работает** из-за того, что не все нужные колонки есть в индексе (`INCLUDE`) или таблица не очищена (vacuum visibility map).

**Устаревшая статистика.** `ANALYZE` после массовых загрузок; autovacuum может отставать на горячих таблицах, их настраиваю отдельно.

**Пагинация через большой `OFFSET`.** Постраничный вывод на глубоких страницах перебирает все пропущенные строки. Лечится keyset-пагинацией: `WHERE (created_at, id) < (:last_created, :last_id)`.

**Коррелированные подзапросы и `NOT IN`** с `NULL`: переписываю в `JOIN` или `NOT EXISTS`.

**Слишком широкий SELECT.** Тянутся большие `text`/`jsonb`-колонки, которые не нужны экрану; гидрация и передача по сети съедают время.

**Блокировки и конкуренция.** Запрос медленный не из-за плана, а из-за ожидания `Lock` в `pg_stat_activity` или `pg_locks`. Это отдельная диагностика.

**Недостаточно памяти для сортировки и хэшей.** Увеличиваю `work_mem` для конкретной сессии или роли, а не глобально.

**Множество JOIN с плохими оценками.** Иногда помогает переписать запрос или разбить на два шага с `CTE`/временной таблицей. В PostgreSQL 12+ `CTE` по умолчанию не барьер оптимизации, а `MATERIALIZED` явно задаёт барьер.

## 4. Проверка результата

После изменения повторяю `EXPLAIN (ANALYZE, BUFFERS)` на той же выборке, сравниваю время и число прочитанных буферов, и проверяю, что запрос не замедлил другие сценарии. Индекс стоит денег: замедляет запись и занимает место, поэтому лишние индексы я тоже ищу через `pg_stat_user_indexes` (поле `idx_scan` близко к нулю).

Изменения в продакшене вношу через миграции с `CREATE INDEX CONCURRENTLY`, чтобы не блокировать запись.

## Итог

Порядок такой: воспроизвести на реалистичных данных, найти самый дорогой запрос по `pg_stat_statements`, прочитать план, понять расхождение между оценкой и фактом, выбрать минимальное изменение (индекс, переписывание, пагинация, статистика), проверить тем же `EXPLAIN` и закрепить тестом или мониторингом.

> ⏱ 12.5s · prompt 32107 tok · output 2188 tok · кэш 30 128+1 975 tok · $0.0016

---

## Q13 · DDD

**Интервьюер:** Что такое агрегат в DDD и как ты определяешь его границы? Приведи пример с объяснением и типовую ошибку при выборе границ.

**Кандидат:**

Агрегат в DDD — это кластер связанных объектов, который рассматривается как единое целое с точки зрения изменений и консистентности. У него один корень (aggregate root), через который извне меняется всё внутреннее состояние, и один инвариант-набор, который должен выполняться в каждый момент времени внутри одной транзакции.

Ключевые свойства:

- **Корень — единственная точка входа.** Внешний код держит ссылку только на корень и не меняет дочерние объекты напрямую.
- **Инварианты внутри границы.** Правила, которые должны быть истинными сразу после каждой операции, проверяются внутри агрегата.
- **Транзакция — в пределах одного агрегата.** Между агрегатами согласованность достигается событиями и асинхронной обработкой (eventual consistency).
- **Ссылки между агрегатами — по идентификатору.** Агрегат ссылается на другой агрегат через его ID, а не через объект.
- **Агрегат небольшой.** Чем меньше объектов в границе, тем меньше конфликтов при параллельных изменениях и проще загрузка.

## Как определяю границы

Задаю три вопроса:

1. **Какой инвариант должен выполняться атомарно?** Если два объекта должны быть согласованы в один момент, они, скорее всего, в одном агрегате.
2. **Что меняется вместе?** Если два объекта почти всегда меняются в одной операции одним пользователем, их логично объединить.
3. **Можно ли допустить задержку согласованности?** Если между ними допустима eventual consistency, границу проводим между агрегатами.

Ещё полезна проверка: если агрегат загружается целиком ради одного поля, границы, скорее всего, слишком широкие.

## Пример: интернет-магазин

Возьмём заказ. Инвариант: сумма заказа равна сумме строк, нельзя добавить строку в уже оплаченный заказ, количество положительное.

```php
final class Order // корень агрегата
{
    /** @var list<OrderLine> */
    private array $lines = [];

    public function __construct(
        private readonly OrderId $id,
        private readonly CustomerId $customerId, // ссылка на другой агрегат по ID
        private OrderStatus $status = OrderStatus::New,
    ) {}

    public function addLine(ProductId $productId, Money $price, int $quantity): void
    {
        if ($this->status !== OrderStatus::New) {
            throw new DomainException('Нельзя менять оплаченный заказ');
        }
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Количество должно быть положительным');
        }
        $this->lines[] = new OrderLine($productId, $price, $quantity);
    }

    public function total(): Money
    {
        return array_reduce(
            $this->lines,
            fn (Money $sum, OrderLine $l) => $sum->add($l->subtotal()),
            Money::zero('RUB'),
        );
    }
}

final readonly class OrderLine
{
    public function __construct(
        public ProductId $productId,
        public Money $unitPrice,
        public int $quantity,
    ) {}

    public function subtotal(): Money
    {
        return $this->unitPrice->multiply($this->quantity);
    }
}
```

Здесь `OrderLine` входит в агрегат `Order`: сумма заказа должна быть согласована со строками, и это проверяется в одной транзакции. `Customer` и `Product` — отдельные агрегаты, на них ссылаются по ID. Изменение склада или профиля покупателя не должно блокировать оформление заказа.

## Типовая ошибка: слишком широкие границы

Частая ошибка — включить в агрегат всё, что связано по смыслу, например `Customer` с его адресами, заказами, корзиной и историей. Тогда:

- каждая операция загружает огромный граф, и растёт число запросов;
- параллельные изменения одного покупателя (добавление адреса и оформление заказа) конфликтуют при оптимистичной блокировке;
- агрегат разбухает, и инварианты становятся трудно понять.

Обратная ошибка — дробить слишком мелко: например, сделать `OrderLine` отдельным агрегатом. Тогда инвариант «сумма заказа согласована со строками» перестаёт гарантироваться транзакцией, и приходится полагаться на eventual consistency там, где нужна строгость.

Правило, которое я использую: граница определяется инвариантами, а не структурой таблиц или связями в ORM. Наличие внешнего ключа ещё не означает, что объекты принадлежат одному агрегату.

### ↳ Углубление

**Интервьюер:** Два процесса одновременно изменяют один и тот же агрегат. Что произойдёт и как ты проектируешь с учётом этого?

**Кандидат:**

Без защиты возможна **потеря обновления (lost update)**: оба процесса читают одну версию агрегата, каждый меняет своё, и второй `flush` молча перезаписывает результат первого. Если изменения касаются разных полей, Doctrine может записать их оба, но если затронуты инварианты (например, сумма строк и итог), агрегат окажется в несогласованном состоянии: каждый процесс проверил правило на своей копии, а вместе они его нарушили.

Пример: два процесса добавляют строки в один заказ. Оба видят сумму 1000, каждый добавляет позицию на 500, в итоге записывается 1500 с одной из строк, потерянной или не учтённой в итоговом значении, если итог хранится денормализованно.

## Стратегии

**1. Оптимистичная блокировка (основной выбор).**

Версия агрегата хранится в корне, и обновление проходит только при совпадении версии:

```php
#[ORM\Entity]
class Order
{
    #[ORM\Version]
    #[ORM\Column(type: 'integer')]
    private int $version = 1;
}
```

Doctrine добавляет в `UPDATE` условие `WHERE version = :expected` и увеличивает версию. Если строка уже изменена, бросается `OptimisticLockException`.

Обработка на уровне приложения: при конфликте команду можно повторить, перечитав агрегат (обычно 2–3 попытки с небольшой задержкой), либо вернуть пользователю ошибку «данные изменились, обновите страницу». Повтор уместен, если команда не зависит от клиентского представления, которое устарело.

Плюсы: без блокировок в БД, хорошо масштабируется, конфликты редки для агрегатов небольшого размера. Минусы: при частых конфликтах растут повторы и ошибки; требуется обработка исключения во всех точках записи.

**2. Пессимистичная блокировка.**

```php
$order = $this->em->find(Order::class, $id, LockMode::PESSIMISTIC_WRITE);
```

Строка блокируется `SELECT ... FOR UPDATE` до конца транзакции. Второй процесс ждёт. Плюсы: конфликтов не возникает, логика проще, когда операция длинная и повторы недопустимы. Минусы: блокировки снижают пропускную способность, а при долгой транзакции и взаимных блокировках появляются deadlock'и; нужна короткая транзакция и стабильный порядок захвата ресурсов.

**3. Атомарные операции в SQL.**

Для простых счётчиков и остатков лучше не читать и писать, а менять на стороне БД:

```sql
UPDATE products SET stock = stock - :qty WHERE id = :id AND stock >= :qty;
```

Проверка `stock >= :qty` в том же запросе защищает от гонки. Минус: правило живёт в SQL, а не в домене, поэтому применяю только для простых, однозначных инвариантов.

**4. Ограничения в БД.**

Как страховка: `CHECK`, `UNIQUE`, внешние ключи. Они не дадут сохранить явно нарушенное правило, но не решают проблему потери обновления для сложных инвариантов.

**5. Сериализация команд на один агрегат.**

Если агрегат меняется часто и конфликты обходятся дорого, команды к одному агрегату направляю в одну очередь или в один воркер по ключу (Messenger с партиционированием, либо `Symfony Lock` на ID). Так конфликтов нет по построению, но появляется задержка и сложность при масштабировании.

## Что выбираю

По умолчанию **оптимистичная блокировка через `#[ORM\Version]`** на корне агрегата, плюс обработка `OptimisticLockException` в обработчике команды с ограниченным числом повторов. Пессимистичную блокировку использую, если конфликты частые, а повтор невозможен или дорог. Атомарный `UPDATE` выбираю для счётчиков и остатков. Важно, что версия должна меняться при любом изменении агрегата, включая строки: поэтому изменения дочерних объектов проходят через корень, и версия корня отражает изменения всего агрегата.

Тест на конкурентность обязателен: два параллельных обработчика должны либо оба успешно отработать без нарушения инварианта, либо один должен получить конфликт и корректно повториться.

> ⏱ 9.1s · prompt 34347 tok · output 1601 tok · кэш 32 103+2 240 tok · $0.0014

---

## Q14 · DDD

**Интервьюер:** Как ты применяешь доменные события: когда публикуешь, как обрабатываешь, какие проблемы этот подход решает и какие создаёт? Как доменные события связаны с транзакциями базы данных?

**Кандидат:**

Доменное событие фиксирует факт, который произошёл в домене: `OrderPlaced`, `UserRegistered`, `StockReserved`. Оно записано в прошедшем времени, неизменяемо и описывает, что случилось, а не что нужно сделать. Команда (`SendEmail`) и событие различаются именно этим.

## Когда публикую

Событие порождает агрегат в своём методе, когда меняет состояние и инвариант выполнен:

```php
final class Order
{
    /** @var list<object> */
    private array $events = [];

    public function place(): void
    {
        if ($this->lines === []) {
            throw new DomainException('Пустой заказ нельзя оформить');
        }
        $this->status = OrderStatus::Placed;
        $this->events[] = new OrderPlaced($this->id, $this->total());
    }

    /** @return list<object> */
    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}
```

Агрегат накапливает события в памяти и не публикует их сам. Это важно: домен не знает о шине, БД и транзакциях. Событие — это описание факта, а не вызов инфраструктуры.

## Как обрабатываю

Публикация происходит в слое приложения, после сохранения агрегата:

```php
final readonly class PlaceOrderHandler
{
    public function __construct(
        private OrderRepository $orders,
        private EntityManagerInterface $em,
        private MessageBusInterface $bus,
    ) {}

    public function __invoke(PlaceOrderCommand $cmd): void
    {
        $order = $this->orders->get($cmd->orderId);
        $order->place();

        $this->em->wrapInTransaction(fn () => $this->em->flush());

        foreach ($order->pullEvents() as $event) {
            $this->bus->dispatch($event);
        }
    }
}
```

Обработчики событий делаю отдельными, по одному на реакцию. Побочные эффекты (письмо, индексация, интеграция) выполняю асинхронно через Messenger, а в сообщении передаю идентификаторы, как и для обычных сообщений.

## Что решают события

- **Развязка.** Заказ не знает о письмах, складе и аналитике; добавление реакции не требует правок в `Order`.
- **Явные границы агрегатов.** Между агрегатами связь идёт через факты, а не через прямые вызовы, поэтому eventual consistency становится осознанным решением.
- **Аудит и история.** Поток событий показывает, что и когда произошло.
- **Тестируемость.** Тест агрегата проверяет, что нужное событие появилось, без инфраструктуры.

## Что создают события

- **Потеря событий.** Если коммит прошёл, а `dispatch` упал (процесс убит, очередь недоступна), реакция не произойдёт. Это главная проблема.
- **Ложное срабатывание.** Если событие отправлено до коммита, а транзакция откатилась, подписчики обработали факт, которого не было.
- **Дубликаты.** Повторная доставка требует идемпотентных обработчиков.
- **Порядок.** Гарантий порядка между разными событиями и разными потребителями обычно нет.
- **Сложность отладки.** Цепочка реакций расползается по коду, и понять, что вызвало изменение, сложнее, чем при прямом вызове.
- **Версионирование.** Событие — это контракт. Его поля меняются осторожно, иначе ломаются подписчики, в том числе те, что живут в очереди дольше, чем версия кода.

## Связь с транзакциями

Ключевое правило: **событие должно публиковаться только после успешного коммита**, иначе возникает ложное срабатывание. Но если публиковать после коммита напрямую, возникает обратная проблема: коммит прошёл, а публикация упала, и событие потеряно.

Варианты решения:

1. **Dispatch после коммита** (как в примере выше). Просто, но не гарантирует доставку при сбое между коммитом и отправкой. Подходит, когда потеря реакции допустима или восстанавливается периодической сверкой.
2. **Transactional outbox.** Событие записывается в таблицу `outbox` в той же транзакции, что и агрегат. Отдельный процесс читает таблицу и публикует события, помечая их отправленными. Это даёт гарантию «состояние изменилось — событие будет доставлено хотя бы один раз». Цена: дополнительная таблица, поллер и идемпотентные потребители. Для критичных реакций (списание, письмо после оплаты) выбираю именно это.
3. **Doctrine lifecycle events** (`postFlush`) для публикации внутри того же процесса. Удобно, но смешивает инфраструктуру с доменом и тоже не решает проблему падения между коммитом и отправкой.

Дополнительно: не выполняю внешние вызовы внутри транзакции (например, HTTP-запросы к платёжному шлюзу в обработчике события, который вызывается до коммита). Это удлиняет транзакцию, блокирует строки и не откатывается вместе с БД.

## Итог

Агрегат фиксирует факты, слой приложения публикует их после сохранения, побочные эффекты выполняются асинхронно и идемпотентно. Для реакций, которые нельзя потерять, добавляю outbox. Доменные события не заменяют транзакцию, а опираются на неё: ничего не публикуется, пока изменение не зафиксировано.

> ⏱ 10.6s · prompt 37584 tok · output 1907 tok · кэш 35 992+1 588 tok · $0.0015

---

## Q15 · DDD

**Интервьюер:** Чем Value Object отличается от Entity в DDD? Как решаешь, к чему отнести ту или иную концепцию домена? Что такое единый язык (ubiquitous language) и как он влияет на код?

**Кандидат:**

Entity и Value Object различаются тем, как определяется их identity: тождественность объекта или его значение.

## Entity

Entity определяется **идентичностью**. Два объекта с одинаковыми атрибутами, но разными ID, — разные сущности. Состояние со временем меняется, а объект остаётся тем же: клиент остаётся клиентом, даже если сменил адрес и фамилию.

```php
final class Customer
{
    public function __construct(
        private readonly CustomerId $id,
        private Email $email,
    ) {}

    public function changeEmail(Email $email): void
    {
        $this->email = $email;
    }

    public function equals(self $other): bool
    {
        return $this->id->equals($other->id);
    }
}
```

## Value Object

Value Object определяется **значением**. Он не имеет идентичности, неизменяем и всегда валиден. Два объекта с одинаковыми полями взаимозаменяемы: две купюры по 100 рублей — одно и то же.

```php
final readonly class Money
{
    public function __construct(
        public int $amountCents,
        public string $currency,
    ) {
        if ($amountCents < 0) {
            throw new InvalidArgumentException('Сумма не может быть отрицательной');
        }
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amountCents + $other->amountCents, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amountCents === $other->amountCents
            && $this->currency === $other->currency;
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new DomainException('Валюты не совпадают');
        }
    }
}
```

Изменение VO означает замену на новый экземпляр. Сравнение идёт по значению.

## Как решаю, к чему отнести концепцию

Задаю несколько вопросов:

1. **Нужно ли отличать два экземпляра, даже если их данные совпадают?** Да — Entity (два заказа с одинаковой суммой остаются разными заказами). Нет — VO (два одинаковых диапазона дат взаимозаменяемы).
2. **Меняется ли объект со временем, и нужно ли его отслеживать?** Если да, это, скорее всего, Entity с жизненным циклом.
3. **Нужна ли история или ссылка извне?** На Entity ссылаются по ID, на VO передают целиком.
4. **Что будет, если заменить экземпляр на равный ему?** Если ничего не изменится — VO.

Полезная эвристика: сначала считаю всё VO, а Entity выделяю, только когда возникает потребность в идентичности. Ошибка «всё сущность» приводит к изменяемым объектам с размазанной логикой, ошибка «всё VO» — к потере идентичности, когда нужно, например, отслеживать номер заказа.

## Единый язык (Ubiquitous Language)

Единый язык — общий словарь, который используют разработчики, аналитики и бизнес в речи, документации и коде. Если бизнес говорит «резерв», в коде должен быть `reserve()`, а не `lockItems()`. Если говорят «оформить заказ», а не «создать запись», название метода и класса повторяют этот термин.

Как это влияет на код:

- **Имена классов и методов** совпадают с терминами домена. Код читается как описание процесса для бизнеса.
- **Модели отражают правила**, которые обсуждают эксперты. Если эксперт говорит «заказ нельзя отменить после отправки», это становится методом `cancel()` с проверкой статуса, а не флагом в контроллере.
- **Расхождения видны.** Если разработчик называет одно и то же двумя словами (`Client` в коде, «покупатель» в речи), это сигнал, что модель понята неточно, и её стоит уточнить.
- **Контексты разделяются.** Один и тот же термин может значить разное в разных контекстах («заказ» в продажах и в доставке). Тогда единый язык действует внутри bounded context, а не на всю компанию.

Единый язык стоит поддерживать в документации (глоссарий в репозитории), на встречах и в код-ревью. Если в ревью обсуждение сводится к «как назвать» и выявляет расхождение с бизнесом, это хороший признак: модель становится точнее.

> ⏱ 9.5s · prompt 39563 tok · output 1520 tok · кэш 37 580+1 979 tok · $0.0014

---

## Q16 · Чистая архитектура

**Интервьюер:** Опиши структуру типичного PHP-приложения по принципам чистой архитектуры: какие слои, что в каждом лежит, каково правило зависимостей между ними. Куда попадут Doctrine, Symfony-контроллер и обработчик очереди? Как следить, что границы слоёв не нарушаются?

**Кандидат:**

Чистая архитектура строится вокруг одного правила: **зависимости в коде направлены только внутрь**, от инфраструктуры к домену. Внутренние слои ничего не знают о внешних: домен не импортирует Doctrine, Symfony или HTTP.

## Слои

**1. Domain (домен).** Сущности, агрегаты, value objects, доменные события, доменные сервисы, интерфейсы репозиториев (как контракты, описанные в терминах домена). Чистый PHP, без фреймворка и ORM. Это самое стабильное ядро.

**2. Application (прикладной слой).** Сценарии использования (use cases): команды и их обработчики, запросы, порты для внешних систем (интерфейсы `Mailer`, `PaymentGateway`, `SearchIndex`). Здесь оркестрируется работа домена: загрузить агрегат, вызвать метод, сохранить, опубликовать события. Здесь же граница транзакции.

**3. Infrastructure (инфраструктура).** Реализации портов и репозиториев: Doctrine-репозитории, маппинг, HTTP-клиенты, адаптеры очередей, файловая система, мейлер. Здесь лежат все `#[ORM\...]`-атрибуты, если не выбран отдельный маппинг через XML/YAML.

**4. Presentation / Delivery (внешний интерфейс).** Контроллеры, консольные команды, обработчики сообщений как точки входа, DTO запросов и ответов, сериализация, шаблоны. Вызывают application-слой и ничего не решают сами.

Symfony-фреймворк и `config/` относятся к внешнему кольцу: это сборка приложения, а не бизнес-логика.

## Куда что попадает

- **Doctrine.** Маппинг и реализации репозиториев — в **Infrastructure**. Сущности в домене остаются чистыми; если без атрибутов ORM не обойтись, маппинг вынесен в отдельные XML/YAML-файлы рядом с инфраструктурой. Интерфейс репозитория (`OrderRepository`) — в домене или в application, реализация с `EntityManager` — в infrastructure.
- **Symfony-контроллер.** В **Presentation**. Он разбирает HTTP-запрос, формирует команду, вызывает обработчик из application и возвращает ответ. Без `EntityManager`, без бизнес-условий.

```php
final class PlaceOrderController
{
    public function __invoke(Request $request, PlaceOrderHandler $handler): JsonResponse
    {
        $handler(new PlaceOrderCommand(
            orderId: $request->request->get('order_id'),
        ));

        return new JsonResponse(status: 202);
    }
}
```

- **Обработчик очереди.** Messenger-handler — это точка входа, как контроллер. Он в **Presentation** (или отдельном слое `Delivery`), а вызывает тот же use case из application. Не нужно дублировать логику в обработчике сообщений.

```php
#[AsMessageHandler]
final readonly class PlaceOrderMessageHandler
{
    public function __construct(private PlaceOrderHandler $handler) {}

    public function __invoke(PlaceOrderMessage $message): void
    {
        ($this->handler)(new PlaceOrderCommand($message->orderId));
    }
}
```

Так HTTP и очередь используют один сценарий, а бизнес-логика не зависит от способа вызова.

## Правило зависимостей

```
Presentation -> Application -> Domain
Infrastructure -> Application, Domain (реализует порты)
```

Domain не зависит ни от кого. Application зависит от Domain и от собственных портов. Infrastructure реализует порты, поэтому зависит от Application и Domain, а не наоборот. Инверсия достигается интерфейсами: application объявляет `PaymentGateway`, infrastructure предоставляет `HttpPaymentGateway`, а Symfony связывает их через DI (alias в `services.yaml`).

## Как следить за границами

1. **Статический анализ зависимостей.** Инструменты `deptrac` (PHP) или `phparkitect` позволяют описать допустимые направления и падать в CI при нарушении. Например: «Domain не может зависеть от `Doctrine\*` и `Symfony\*`».
2. **Архитектурные тесты.** Тест, который проверяет, что классы в `Domain` не используют `App\Infrastructure\*` и атрибуты `Doctrine\ORM\Mapping`.
3. **Проверка при ревью.** В `use`-блоке доменного класса не должно быть фреймворка. Это быстрый сигнал.
4. **Тесты домена без ядра Symfony.** Если доменный тест требует `KernelTestCase` или БД, граница уже нарушена.
5. **Правило на отказ.** Контроллер с `EntityManager`, сервис с `Request`, сущность с `#[Route]` — типичные признаки, что логика утекла в неверный слой.

Важно не доводить до догматизма: слои нужны там, где есть сложная логика и долгий срок жизни. Для CRUD-модуля без правил достаточно меньшего числа слоёв, но направление зависимостей стоит соблюдать везде.

> ⏱ 10.8s · prompt 41191 tok · output 1823 tok · кэш 39 559+1 628 tok · $0.0015

---

## Q17 · Тестирование

**Интервьюер:** Какую стратегию тестирования выберешь для Symfony-приложения с PostgreSQL: пирамида тестов, типы тестов, что мокаешь, а что нет? Как поддерживать тесты быстрыми и стабильными?

**Кандидат:**

Стратегия строится на пирамиде, но с акцентом на то, что тесты дешёвые, быстрые и показывают реальные ошибки. Для Symfony с PostgreSQL я обычно выбираю такую форму: много unit-тестов домена, заметный слой интеграционных тестов с реальной PostgreSQL, и небольшой набор функциональных тестов HTTP и end-to-end сценариев.

## Типы тестов

**1. Unit (домен и чистые части application).** Агрегаты, value objects, доменные сервисы, политики, voters без БД. Выполняются за миллисекунды, без ядра Symfony и без базы. Здесь проверяю инварианты и переходы состояний.

```php
public function test_cannot_add_line_to_paid_order(): void
{
    $order = Order::newForCustomer(CustomerId::generate());
    $order->addLine(ProductId::generate(), Money::rub(1000), 1);
    $order->pay();

    $this->expectException(DomainException::class);
    $order->addLine(ProductId::generate(), Money::rub(500), 1);
}
```

**2. Интеграционные (репозитории, маппинг, запросы).** Реальный PostgreSQL, обычно в Docker через testcontainers или сервис в CI. Проверяют маппинг Doctrine, ограничения схемы, индексы, поведение `OptimisticLockException`, сложные запросы. Здесь SQLite не подхожу: поведение типов, `ON CONFLICT`, `citext`, `jsonb`, частичные индексы отличается, и тесты будут врать.

**3. Application-тесты (use cases).** Handler вызывается напрямую с реальными репозиториями на тестовой БД либо с in-memory реализациями портов. Проверяю сценарий целиком: загрузка, изменение, сохранение, публикация событий.

**4. Функциональные тесты HTTP (WebTestCase).** Несколько на каждый ключевой эндпоинт: статусы, валидация, авторизация (voters и firewall), формат ответа. Не проверяю бизнес-правила через HTTP, это уже покрыто ниже.

**5. End-to-end.** Немного, на критичные пути (регистрация, оплата). Запускаю в отдельном job CI, не в каждом коммите.

## Что мокаю, а что нет

**Мокаю:** внешние системы (платёжный шлюз, почта, поисковый индекс, HTTP-клиенты), часы (`ClockInterface`), генерацию ID и случайных значений, очереди при тестировании обработчиков событий (проверяю, что сообщение отправлено, а не что его обработали).

**Не мокаю:** собственные value objects и агрегаты, Doctrine в интеграционных тестах (мок EntityManager проверяет вызовы, а не поведение), PostgreSQL, собственные сервисы домена. Мок внутреннего класса, который легко создать реально, превращает тест в копию реализации.

Порядок вопроса простой: мокаю то, что медленное, недетерминированное или внешнее. Остальное запускаю реально.

## Изоляция данных в БД

- Каждый тест выполняется в транзакции, которая откатывается в `tearDown` (`dama/doctrine-test-bundle`). Это быстро и не требует очистки таблиц.
- Для тестов, которые сами требуют коммита (проверка `OptimisticLockException` между двумя EntityManager, поведение outbox), явно очищаю нужные таблицы или использую отдельную схему.
- Схему создаю миграциями, а не `schema:update`, чтобы тесты проверяли то же, что продакшен.
- Тестовые данные строю фабриками (builders), а не фикстурами с общими ID.

## Скорость и стабильность

- **Не поднимать ядро без нужды.** Unit-тесты не наследуют `KernelTestCase`. Ядро Symfony грузится один раз на набор, а не на каждый тест.
- **Один PostgreSQL на прогон.** Контейнер поднимаю один раз, миграции накатываю один раз, дальше только откаты транзакций. Для параллельного запуска (`paratest`) каждому процессу своя база.
- **Детерминированность.** Фиксированные часы, сиды для генераторов, никаких `sleep`. Асинхронность проверяю синхронным транспортом (`sync://`) в тестовом окружении или явной обработкой очереди.
- **Нет зависимостей между тестами.** Порядок выполнения не важен; проверяю запуском в случайном порядке (`--order-random`).
- **Флаки лечу сразу.** Нестабильный тест либо чинится, либо отключается с задачей. Оставлять его «до повтора» нельзя: через месяц команда перестанет верить красному CI.
- **Контроль числа запросов.** Тест на N+1 (счётчик запросов, как описано ранее) защищает от регрессий производительности, не замедляя тесты заметно.
- **Бюджет времени.** Смотрю `--profile` в PHPUnit и самые медленные тесты переписываю или переношу на более низкий уровень.

## Инструменты и CI

PHPUnit, dama/doctrine-test-bundle, Symfony BrowserKit/`WebTestCase`, testcontainers или сервис PostgreSQL в CI, PHPStan на высоком уровне как часть тестирования без запуска, deptrac или архитектурные тесты на границы слоёв. В CI порядок: статический анализ и unit (быстро, на каждый коммит), затем интеграционные и HTTP, затем E2E (на merge в основную ветку или по расписанию).

## Итог

Много быстрых тестов домена, достаточно интеграционных с реальной PostgreSQL, немного HTTP и E2E. Мокаю только то, что внешнее или недетерминированное. Изоляция через транзакции, детерминированные данные и немедленная починка флаков делают набор быстрым и доверенным.

> ⏱ 12.9s · prompt 43096 tok · output 2068 tok · кэш 41 187+1 905 tok · $0.0017

---

## Q18 · Конкурентность

**Интервьюер:** Два пользователя одновременно изменяют одну и ту же запись в базе. Какие проблемы могут возникнуть и какие способы защиты от них ты знаешь? Сравни подходы и опиши, когда какой выбираешь.

**Кандидат:**

Когда два пользователя меняют одну запись, главная опасность в том, что каждый работает со своей копией данных и не видит изменений другого. Конфликт не всегда виден: иногда одна из правок молча теряется, а иногда оба изменения по отдельности корректны, но вместе нарушают правило.

## Какие проблемы возможны

**1. Потеря обновления (lost update).** Оба процесса читают версию 1, каждый меняет своё поле, второй `UPDATE` перезаписывает результат первого. Это самая частая проблема, и она возникает даже в `READ COMMITTED`, которая используется в PostgreSQL по умолчанию. Причина в цикле «прочитать, изменить в памяти, записать».

```
A: SELECT balance -> 100
B: SELECT balance -> 100
A: UPDATE ... SET balance = 100 - 30  -> 70
B: UPDATE ... SET balance = 100 + 50  -> 150   (списание A потеряно)
```

**2. Нарушение инварианта через несколько строк (write skew).** Каждый процесс проверяет правило на своей копии, и оба проходят проверку, но вместе правило нарушают. Пример: два врача одновременно снимают себя с дежурства, и правило «хотя бы один дежурный» не проверяется, потому что каждый видит другого на смене.

**3. Неповторяемое чтение и фантомы.** В одной транзакции два чтения одних данных дают разный результат. Опасно для расчётов, которые читают данные несколько раз.

**4. Взаимная блокировка (deadlock).** Два процесса захватывают блокировки в разном порядке и ждут друг друга. PostgreSQL прерывает одну из транзакций с ошибкой `40P01`.

**5. Конфликт при записи, который не виден пользователю.** Второй пользователь получает ошибку или тихо перезаписывает чужие данные, и никто не знает, что произошло.

## Способы защиты

### 1. Оптимистичная блокировка через версию

В таблицу добавляется колонка версии. `UPDATE` проходит только при совпадении версии, иначе изменение отклоняется.

```php
#[ORM\Version]
#[ORM\Column(type: 'integer')]
private int $version = 1;
```

Doctrine сам добавляет `WHERE version = :expected` и обрабатывает конфликт через `OptimisticLockException`. Вручную можно то же сделать через `UPDATE ... WHERE id = :id AND version = :v`, проверяя количество затронутых строк.

- **Плюсы:** нет блокировок в БД, высокая пропускная способность, почти бесплатно при редких конфликтах.
- **Минусы:** при конфликте нужна обработка (повтор или сообщение пользователю), при частых конфликтах растут повторы. Защищает только от записей, которые проходят через механизм версии: прямой SQL в обход версии ломает гарантию.
- **Выбор:** основной вариант для веб-форм и агрегатов, где конфликты редки, а пользователь может перечитать данные.

### 2. Пессимистичная блокировка

Строка блокируется до конца транзакции, второй процесс ждёт.

```php
$account = $this->em->find(Account::class, $id, LockMode::PESSIMISTIC_WRITE);
```

Это `SELECT ... FOR UPDATE`. Для чтения без запрета на запись есть `FOR SHARE`, в Doctrine `LockMode::PESSIMISTIC_READ`.

- **Плюсы:** конфликтов не возникает, логика проще, повторы не нужны.
- **Минусы:** ожидание снижает пропускную способность, длинные транзакции держат блокировки, возможны deadlock'и, которые нужно обрабатывать повтором.
- **Выбор:** когда конфликты частые, операция короткая и повтор невозможен или дорог (списание денег, резерв последней единицы товара, если нельзя полагаться на атомарный `UPDATE`).

### 3. Атомарное изменение на стороне SQL

Вместо «прочитать, посчитать, записать» выполняется одна операция, которая сама проверяет условие.

```sql
UPDATE accounts SET balance = balance - :amount
WHERE id = :id AND balance >= :amount;
```

Если затронуто 0 строк, значит, денег не хватило. Это не даёт потери обновления и не требует отдельной блокировки.

- **Плюсы:** быстро, одна атомарная операция, нет окна между чтением и записью.
- **Минусы:** правило живёт в SQL и плохо выражает сложную доменную логику.
- **Выбор:** счётчики, остатки, баланс, инкременты. То, что можно выразить одной арифметической операцией с условием.

### 4. Уровень изоляции SERIALIZABLE

В PostgreSQL `SERIALIZABLE` (через Snapshot Isolation с предикатными блокировками) обнаруживает даже write skew. Вместо блокировки база отменяет одну из транзакций с ошибкой `40001`, и приложение должно повторить её целиком.

```php
$this->em->wrapInTransaction(function () use ($work): void {
    $this->em->getConnection()->executeStatement(
        'SET TRANSACTION ISOLATION LEVEL SERIALIZABLE'
    );
    $work();
});
```

Повтор при `40001` обязателен, иначе ошибка уходит пользователю.

- **Плюсы:** защищает от широкого класса аномалий без явных блокировок, правило читается в коде как последовательное.
- **Минусы:** частые откаты при конкуренции, нужна обёртка с повтором, выше нагрузка на БД.
- **Выбор:** сложные инварианты по нескольким строкам (дежурства, бронирования, лимиты), где явные блокировки трудно написать правильно.

### 5. Ограничения в БД

`UNIQUE`, `CHECK`, exclusion constraints не дают сохранить явно нарушенное правило независимо от гонки. Например, два бронирования одного номера на пересекающиеся даты отсекает `EXCLUDE USING gist`.

- **Выбор:** всегда как страховка. Сами по себе не решают потерю обновления для расчётных полей.

### 6. Сериализация на уровне приложения

Команды для одного ресурса выполняются последовательно: через `Symfony Lock` по ключу ресурса или через очередь с партиционированием по ID.

```php
$lock = $this->lockFactory->createLock('account:' . $id);
$lock->acquire(true);
try {
    // изменение
} finally {
    $lock->release();
}
```

- **Плюсы:** конфликтов в БД нет, логика изолирована.
- **Минусы:** задержка, сложность при масштабировании, блокировка в приложении не защищает от записей в обход него.
- **Выбор:** когда ресурс горячий, и повтор или откат дороже ожидания в очереди.

### 7. Advisory-блокировки PostgreSQL

`pg_advisory_xact_lock(key)` блокирует условный ключ, а не строку. Удобно, когда защищается не одна строка, а логическая сущность или группа строк.

- **Выбор:** когда нужна блокировка по бизнес-ключу, который не совпадает с первичным ключом строки, и нет общего корня для `FOR UPDATE`.

## Как выбираю

1. **Конфликты редки, пользователь может повторить:** оптимистичная версия на агрегате.
2. **Конфликты частые, повтор недопустим (деньги, последний остаток):** пессимистичная блокировка на короткой транзакции или атомарный `UPDATE` с условием, если операция укладывается в одну арифметику.
3. **Нарушение правила через несколько строк (write skew):** `SERIALIZABLE` с повтором или явная блокировка общего родителя.
4. **Ресурс очень горячий, очередь допустима:** сериализация команд по ключу.
5. **Всегда:** ограничения в БД и тесты на конкурентность, которые запускают два параллельных сценария и проверяют, что инвариант сохранён.

Важно не смешивать несовместимые механизмы без причины. Например, версия на агрегате не защищает от прямого `UPDATE` в обход домена, а пессимистичная блокировка не спасает от гонки между сервисами, которые не используют одну и ту же блокировку. Выбор зависит от того, какие записи проходят через какой путь.

> ⏱ 17.9s · prompt 45245 tok · output 3296 tok · кэш 43 092+2 149 tok · $0.0023

---

## Q19 · Git и процесс

**Интервьюер:** Опиши свой git-процесс от постановки задачи до продакшена: ветвление, формат коммитов, pull request, ревью, релизы. Объясни, почему так, а не иначе.

**Кандидат:**

Ниже процесс, который я считаю рабочим для команды из нескольких разработчиков с продакшеном, который нельзя ломать. Это не единственно правильный вариант, но каждый шаг объясняю причиной.

## 1. Постановка задачи

Задача описывается в трекере с целью и критериями приёмки. Если задача касается инварианта, миграции или публичного контракта, в описании сразу пишу, что именно меняется и как проверяется. Причина: git-история отражает намерение только тогда, когда намерение было сформулировано до кода.

## 2. Ветвление: trunk-based с короткими ветками

Основная ветка `main` всегда зелёная и готова к выкладке. Работа ведётся в коротких ветках (`feature/...`, `fix/...`, `chore/...`), которые живут от нескольких часов до пары дней и вливаются в `main` через pull request.

Почему не длинный git-flow с `develop` и `release/*`: длинные ветки копят расхождения, слияние превращается в отдельную работу, а интеграционные ошибки находятся поздно. Короткие ветки дешевле в слиянии и проще для CI. Long-lived ветка допустима только для большой миграции, и тогда за ней следит отдельный человек и она регулярно подтягивает `main`.

Для неблагополучных ситуаций используется `hotfix/...` от той же `main`, но с коротким циклом: исправление, PR, ускоренное ревью, выкладка.

Имя ветки содержит идентификатор задачи: `feature/ORD-142-partial-refund`. Так связь коммитов с трекером видна без поиска.

## 3. Локальная работа и коммиты

Коммиты маленькие и атомарные: один логический шаг, который проходит тесты. Миграция, изменение домена и его тесты могут быть разными коммитами, но каждый коммит собирается и тестируется. Это важно для `git bisect`.

Формат сообщений Conventional Commits с описанием на русском или английском, как принято в команде:

```
feat(orders): add partial refund for paid orders

Refund is allowed only within 30 days and cannot exceed
the paid amount minus already refunded. Invariant is enforced
in Order aggregate, the handler only orchestrates.

Refs: ORD-142
```

Заголовок до 72 символов в повелительном наклонении, тело объясняет «почему», а не «что» (что видно в диффе). Ссылка на задачу в футере.

Перед пушем: `git rebase -i` чтобы убрать шум (`fixup`, опечатки), и `git rebase origin/main`, чтобы ветка была актуальной. Причина: линейная история `main` читается проще, а `git blame` и `bisect` работают точнее.

Коммиты с `--no-verify` не делаю: локальные хуки (PHP-CS-Fixer, PHPStan на изменённых файлах) экономят время CI.

## 4. Pull request

PR маленький: обычно до 400 строк диффа. Большой PR ревьюится поверхностно, поэтому крупную задачу режу на последовательные PR, каждый из которых безопасен к выкладке. Например, сначала добавляю новую колонку, затем код, который её пишет, затем код, который её читает, и только потом удаляю старую.

В описании PR: что и зачем, как проверено, есть ли миграция и как она откатывается, влияние на производительность или публичный контракт, скриншоты для UI. Для изменений схемы или запросов прикладываю план `EXPLAIN`.

Чек-лист CI должен быть зелёным до ревью: линтеры, PHPStan, unit и интеграционные тесты с PostgreSQL, deptrac, тесты на количество запросов для затронутых экранов.

## 5. Ревью

Ревьюер проверяет в первую очередь инварианты, границы слоёв, имена из единого языка, обработку ошибок, миграции (блокирующие ли `CREATE INDEX`, есть ли `CONCURRENTLY`) и тесты, которые действительно проверяют поведение, а не реализацию. Стиль уже проверяется автоматикой и в ревью почти не обсуждается.

Правило: автор не мерджит сам свой PR, минимум один одобрение, для критичных модулей (платежи, права доступа) два. Комментарии разделяю на блокирующие и рекомендательные, явно помечая префиксом `nit:`. Спорные места решаем созвоном, а не бесконечной перепиской.

Merge делаю через squash или rebase-merge по политике команды. Squash удобен, когда ветка шумная, rebase-merge сохраняет осмысленные коммиты. Важно, чтобы итоговый коммит в `main` проходил CI.

## 6. Релизы и выкладка

Каждый merge в `main` автоматически собирается и деплоится на staging. В продакшен выкладка управляемая: тег версии по SemVer (`v2.4.0`) и changelog, который генерируется из Conventional Commits (`feat` даёт minor, `fix` даёт patch, `BREAKING CHANGE` даёт major).

Порядок выкладки: миграции обратно совместимы (expand/contract), сначала деплой, потом включение фичи через feature flag, если изменение рискованное. Откат кода возможен без отката схемы, поэтому деструктивные миграции (удаление колонки) отдельным релизом, после того как код перестал её использовать.

Теги ставлю на коммит в `main`, никогда на ветки. Hotfix выпускается как patch-версия с тем же процессом, но с ускоренным ревью.

## Почему так

- **Короткие ветки и маленькие PR** снижают стоимость интеграции и ускоряют ревью: больше вероятность, что ошибку заметят до мерджа.
- **Зелёный `main`** позволяет выкатывать в любой момент и не зависеть от чужой незавершённой работы.
- **Conventional Commits** делают историю машиночитаемой: changelog, версии и поиск регрессий работают без ручной работы.
- **Ревью на инварианты и границы**, а не на стиль, потому что стиль уже проверяет автоматика, а ошибки в модели стоят дороже.
- **Expand/contract для миграций** позволяет откатить код без простоя и без потери данных.

Процесс стоит адаптировать к размеру команды: для двух человек достаточно веток, PR и зелёного CI, а строгие правила о двух одобрениях уместны, когда цена ошибки высокая.

> ⏱ 15.9s · prompt 48265 tok · output 2246 tok · кэш 45 241+3 020 tok · $0.0020

---

## Q20 · Git и процесс

**Интервьюер:** Что для тебя хороший код-ревью: как маркируешь замечания, на что смотришь в первую очередь, как поступаешь при разногласии с автором?

**Кандидат:**

Хороший код-ревью для меня — это такой разбор изменения, после которого код стал понятнее и надёжнее, а команда не потратила на процесс больше времени, чем нужно. Ревью оценивает не только код, но и то, как он впишется в систему через полгода.

## На что смотрю в первую очередь

Порядок важен: сначала то, что дорого исправлять потом.

1. **Инварианты и границы.** Правила домена проверяются там, где им положено (в агрегате, а не в контроллере)? Не нарушены ли границы транзакции и агрегатов? Не появилась ли зависимость домена от Doctrine или Symfony?
2. **Данные и конкурентность.** Миграции: блокируют ли они запись (`CREATE INDEX` без `CONCURRENTLY` на большой таблице), совместимы ли с откатом кода (expand/contract)? Нет ли read-modify-write без версии или блокировки? Нет ли потери обновления?
3. **Ошибки и граница с внешним миром.** Не проглочено ли исключение? Сохранена ли причина (`previous`)? Не утекают ли детали наружу в HTTP-ответ? Нет ли внешних вызовов внутри транзакции?
4. **Производительность на реальных объёмах.** Нет ли N+1 в цикле или шаблоне? Не появился ли запрос без индекса под новый фильтр? Для изменений запросов я прошу `EXPLAIN` на данных, близких к продакшену.
5. **Безопасность.** Проверка прав через voter или `#[IsGranted]` на всех точках входа, включая обработчики сообщений и консольные команды. Валидация входных данных на границе.
6. **Тесты.** Проверяют ли они поведение, а не реализацию? Есть ли тест на инвариант и на граничный случай? Не мокается ли то, что проще запустить реально?
7. **Имена и читаемость.** Соответствуют ли термины единому языку? Понятно ли, что делает метод без чтения его тела?

Стиль, форматирование и порядок импортов сюда не входят: их проверяет автоматика, и обсуждать их вручную — пустая трата времени.

## Как маркирую замечания

Каждое замечание явно помечаю, чтобы автор понимал, что обязательно, а что можно не трогать:

- **Без префикса или `blocker:`** — нарушение, из-за которого PR нельзя мерджить: баг, потеря данных, нарушение инварианта, уязвимость.
- **`suggestion:`** — улучшение, которое я предлагаю, но не настаиваю. Автор может отказаться с объяснением.
- **`nit:`** — мелочь: имя, опечатка, стиль, который не покрыт линтером. Не блокирует.
- **`question:`** — я не понимаю решения и хочу узнать причину. Это не обвинение: иногда в коде есть контекст, которого нет у меня.
- **`praise:`** — отмечаю удачные решения. Это не формальность: так команда видит, какие подходы стоит повторять.

Замечание пишу с объяснением «почему», а не просто «переделай». Например, не «вынеси в сервис», а «здесь правило скидки живёт в контроллере, поэтому его не проверит тест домена и его легко обойти из консольной команды; можно перенести в `Order::applyDiscount()`». Если есть конкретный пример поведения, которое сломается, привожу его.

Если замечаний много и они касаются одного корня, пишу одно общее замечание с примерами, а не десять одинаковых комментариев.

## Разногласия с автором

Исхожу из того, что автор знает контекст лучше меня, а я вижу дополнительные риски. Поэтому:

1. **Сначала уточняю, а не спорю.** Задаю вопрос `question:` и выслушиваю ответ. Часто выясняется, что решение обосновано ограничением, которого я не видел.
2. **Оцениваю цену ошибки.** Если расхождение касается стиля или вкуса, уступаю без споров. Если касается инварианта, данных или безопасности, настаиваю, но аргументирую фактами: сценарий, тест, пример.
3. **Ищу общий критерий.** Вместо «мне так нравится» апеллирую к правилам команды, документации или ADR. Если правила нет, предлагаю его зафиксировать.
4. **Переношу в живое обсуждение, если переписка буксует.** Три раунда комментариев без сдвига — сигнал созвониться или выпустить спор на тимлида.
5. **Принимаю решение автора, если риск приемлем.** Тогда прошу хотя бы зафиксировать это в коде комментарием или в описании PR, и, при необходимости, завести задачу на рефакторинг. Ревью — не суд, цель — качество системы, а не победа в споре.

Если автор не согласен, а я прав по существу, важнее добиться, чтобы проблема была видна и отслеживалась, чем чтобы я остался правым в комментариях.

> ⏱ 12.0s · prompt 50566 tok · output 1733 tok · кэш 48 261+2 301 tok · $0.0016

---

## Q21 · Работа с задачами

**Интервьюер:** Вот сырая проблема от бизнеса: «Пользователи жалуются, что не могут найти свои заказы из прошлого, поддержка завалена обращениями». Сформулируй из неё задачу для исполнителя — так, чтобы её можно было выполнить без уточнений. Что обязательно включишь в формулировку и почему?

**Кандидат:**

Сырая жалоба описывает симптом, а не задачу. «Не могут найти заказы» может означать разное: нет поиска, поиск есть, но не находит, заказы старше определённого срока не показываются, пагинация ломается, фильтры не работают, или пользователь ищет не по тем полям. Исполнитель, получив такую формулировку, будет гадать, и результат почти наверняка не закроет проблему. Поэтому перед задачей нужна короткая диагностика, а сама задача формулируется так, чтобы её можно было принять и проверить.

## Что нужно выяснить до постановки

Не у исполнителя, а у бизнеса и поддержки, за один-два дня:

- Сколько обращений и за какой период, какие формулировки в них. Есть ли паттерн (определённый канал, браузер, срок давности заказа)?
- Что именно пользователь пытается сделать: найти заказ по номеру, по дате, по товару, просмотреть историю?
- Есть ли в продукте поиск или история заказов вообще, и что на них видит пользователь?
- Воспроизводится ли проблема: ищем заказ, который точно существует, и он не находится?

Ответы решают, что за задача: отсутствующая функция, баг или проблема UX. От этого зависит объём работы.

## Что обязательно включаю в формулировку

**1. Проблема и её измеримое следствие.** Не «пользователи жалуются», а «X% пользователей, имеющих заказы старше 90 дней, не могут открыть их через личный кабинет; за последний месяц N обращений в поддержку с этой темой». Без цифры нельзя понять, стоит ли задача усилий и закрыта ли она.

**2. Конкретный сценарий, воспроизводимый вручную.** «Пользователь с ID X, заказ №Y от марта, в разделе "Мои заказы" не отображается; поиск по номеру Y возвращает пустой список». Исполнитель должен уметь повторить проблему за минуту. Это же станет критерием приёмки.

**3. Границы: что входит и что не входит.** Например, входит: поиск и фильтрация заказов в личном кабинете по номеру, дате, статусу. Не входит: редизайн страницы, интеграция с CRM поддержки, возврат заказов. Без этого задача разрастается.

**4. Критерии приёмки.** Проверяемые утверждения: «заказ любого срока находится по номеру за время ответа не более 500 мс», «список корректно пагинируется на 10 000 заказов», «для пользователя без заказов показан понятный пустой экран». Каждый пункт можно проверить тестом или вручную.

**5. Нефункциональные требования.** Объём данных (сколько заказов у самого большого пользователя), ожидаемая нагрузка, допустимое время ответа. Для поиска по истории это часто решает архитектуру: LIKE по таблице, индекс PostgreSQL, полнотекстовый поиск или внешний индекс.

**6. Ограничения и зависимости.** Какие данные нельзя показывать (чужие заказы, персональные поля), требования безопасности (проверка владельца заказа через voter), сроки, связанные релизы.

**7. Что считать завершением.** Метрика после выкладки: снижение обращений в поддержку по теме за две недели, либо доля успешных поисков. Задача без метрики закрывается по факту merge, а проблема остаётся.

**8. Контакты и источники.** Кто владелец продукта, кто ответит на вопросы, где лежат логи обращений и примеры.

## Пример итоговой формулировки

> **Задача:** Добавить в личный кабинет поиск заказов по номеру и диапазону дат с пагинацией.
>
> **Проблема:** за последний месяц 240 обращений в поддержку, где пользователи не могут найти заказ старше 60 дней (выгрузка тикетов во вложении). Воспроизведение: пользователь с ID 1042 ищет заказ №58731 от 12.01 — список пуст, хотя заказ есть в БД.
>
> **Входит:** поиск по номеру (точное совпадение) и по диапазону дат, пагинация по 20 записей, понятный пустой результат. **Не входит:** полнотекстовый поиск по товарам, изменение дизайна карточки заказа, интеграция с поддержкой.
>
> **Нефункциональные:** самый большой аккаунт — 18 000 заказов; ответ не дольше 500 мс на p95; доступ только к своим заказам (проверка через voter).
>
> **Приёмка:** (1) заказ №58731 находится по номеру; (2) поиск по диапазону возвращает заказы всех сроков давности; (3) на 18 000 заказах `EXPLAIN` показывает индексный доступ, без Seq Scan; (4) чужой заказ не возвращается ни при каких параметрах; (5) тест на количество запросов не превышает 3.
>
> **Метрика успеха:** обращения по теме «не найду заказ» снижаются на 80% за 2 недели после релиза.
>
> **Владелец:** продуктовый менеджер Анна; вопросы по данным — к поддержке.

Главное правило: если исполнитель должен задавать вопросы, чтобы понять, что делать и когда закончить, формулировка ещё не готова.

### ↳ Углубление

**Интервьюер:** Какие вопросы ты задал бы заказчику до начала работы над этой задачей?

**Кандидат:**

Вопросы стоит разбить по тому, что они решают: что именно строить, какие данные есть, какие ограничения, как измерить успех. Часть ответов получу из логов и данных сам, но спросить всё равно нужно, потому что ответы меняют объём работы.

## Про саму проблему

1. Откуда известно, что пользователи «не могут найти» заказы? Есть ли выгрузка обращений в поддержку с формулировками и датами, или это пересказ одного-двух случаев?
2. Что пользователь в этих обращениях пытается сделать: найти конкретный заказ, вспомнить состав покупки, узнать статус, получить документ?
3. Знает ли поддержка, в чём проблема на их стороне? Например, поддержка сама ищет заказы в админке по другим полям, и это создаёт ощущение, что клиент не может найти.
4. Сколько обращений приходит в месяц и тренд: это давняя проблема или появилась после конкретного релиза?

## Про текущее поведение

5. Что сейчас видит пользователь в личном кабинете: есть ли история заказов, есть ли поиск, сколько записей показывается на странице?
6. Есть ли заказы старше определённого срока, которые не показываются вообще, например из-за архивации, очистки или смены системы?
7. Что со старыми заказами, которые импортированы из другой системы или созданы до миграции? Могут ли у них отсутствовать поля, по которым будет поиск?
8. Есть ли у заказа номер, который пользователь видит и может назвать? В каком формате он выглядит, и совпадает ли он с тем, что написано в письмах и на чеке?

## Про объём и нагрузку

9. Какой максимальный объём заказов у одного пользователя? Есть ли корпоративные аккаунты с тысячами заказов?
10. Сколько всего заказов в системе и как быстро растёт таблица?
11. Какая допустимая задержка ответа для этого экрана, и есть ли опыт медленной работы текущих страниц?

## Про требования к функции

12. Какие критерии поиска нужны в первую очередь: номер, дата, сумма, товар, статус? Что из этого обязательно, а что «хорошо бы»?
13. Нужен ли поиск по товарам внутри заказа (названию, артикулу)? Это уже отдельная и гораздо более тяжёлая задача.
14. Нужна ли сортировка и фильтрация по статусу, или достаточно списка с хронологией?
15. Должен ли поиск работать с опечатками и частичным совпадением, или точного совпадения достаточно?
16. Нужно ли показывать заказы гостевых покупок, если пользователь регистрировался позже, или сопоставлять их по email?

## Про ограничения и безопасность

17. Какие поля заказа нельзя показывать в результатах поиска и в каких случаях (например, данные других участников, частично скрытые реквизиты)?
18. Нужна ли дополнительная проверка, например повторная аутентификация при доступе к старым заказам или к документам?
19. Есть ли юридические требования к хранению и отображению истории заказов, например срок показа?

## Про успех и сроки

20. Как поймём, что задача решена: какая метрика, за какой период, какой целевой уровень?
21. Есть ли связанные планы, которые могут конфликтовать: переезд на новую платформу, смена формата номера заказа, другой релиз того же экрана?
22. Кто принимает решение о приёмке, кто отвечает за данные, и к кому обращаться, если возникнут вопросы по бизнес-правилам?

## Что я сделаю сам до разговора

Прежде чем задавать эти вопросы, посмотрю логи поиска и запросов в личном кабинете, сопоставлю несколько тикетов с данными, проверю, существуют ли заказы старше 60 дней в выборке и не отфильтровываются ли они в запросе. Это позволит спрашивать по конкретике: не «есть ли проблема», а «вот три заказа, которые не находятся, вот запрос, который их отбрасывает».

> ⏱ 12.0s · prompt 52414 tok · output 1835 tok · кэш 50 562+1 848 tok · $0.0017

---

## Q22 · Работа с задачами

**Интервьюер:** Ты выполняешь задачу с чёткими критериями приёмки. По ходу работы выясняется: а) нашёл попутный баг, не связанный с задачей; б) понял, что один из критериев приёмки противоречит цели задачи. Что делаешь в каждом случае и почему?

**Кандидат:**

Оба случая решаю по одному принципу: не расширять молча свою задачу и не менять критерии молча. Всё, что выходит за её рамки, должно стать видимым для тех, кто принимает решение.

## а) Попутный баг, не связанный с задачей

Сначала оцениваю баг по двум осям: насколько он серьёзен и насколько он связан с моей работой.

**Если баг серьёзный** (утечка чужих данных, потеря заказов, падение оформления покупки), я останавливаюсь на нём независимо от задачи. Сообщаю владельцу продукта и тимлиду в тот же день, с воспроизведением и оценкой масштаба. Если есть риск прямо сейчас, предлагаю временную защиту (отключить фичу флагом, закрыть эндпоинт), но не чиню в одиночку то, что затрагивает чужую зону без согласования.

**Если баг несерьёзный**, я его не исправляю в этом же PR. Причины:

- PR с двумя несвязанными изменениями ревьюится хуже и откатывается сложнее: если поиск сломает что-то, откат потянет и правку бага;
- критерии приёмки описывают ожидаемое поведение задачи, а не всё, что можно улучшить;
- исправление может иметь собственные последствия, которые кто-то должен оценить.

Вместо этого я заношу баг в трекер с воспроизведением, ссылкой на место в коде, ожидаемым и фактическим поведением и оценкой влияния. Если баг мешает моей работе, указываю это в задаче и, по согласованию, исправляю отдельным коммитом или отдельным PR, который можно выкатить раздельно.

Отдельно: если баг обнаружен, но не воспроизводится в продакшене и не влияет на данные, я всё равно фиксирую его как запись. Молчаливо пропущенный баг через месяц всплывает снова, уже как инцидент.

## б) Один критерий приёмки противоречит цели задачи

Например, критерий «заказ любого срока находится за 500 мс на 18 000 заказах без Seq Scan», а цель — поиск по диапазону дат, где индекс по дате не даёт выигрыша, или критерий требует точного совпадения по номеру, который в старых заказах нормализован иначе.

Здесь я не выбираю сам, какой из двух пунктов «важнее»: это решение заказчика. Но и не продолжаю работу, притворяясь, что противоречия нет. Порядок такой:

1. **Фиксирую противоречие конкретно.** Не «критерии не сходятся», а «критерий 3 требует индексный доступ при поиске по диапазону дат, но при выборке диапазона в 60% таблицы планировщик выберет Seq Scan, и это корректный выбор; вот `EXPLAIN` на тестовых данных».
2. **Останавливаю работу над затронутой частью**, а не всей задачей. Остальные части, которые не зависят от спорного критерия, продолжаю.
3. **Предлагаю варианты с ценой каждого.** Например: изменить критерий на «p95 ≤ 500 мс» вместо «без Seq Scan»; ограничить диапазон дат по умолчанию; добавить составной индекс под частый сценарий с оценкой стоимости записи.
4. **Согласую изменение критерия письменно** с владельцем задачи. Изменение фиксируется в задаче с датой и причиной, чтобы приёмка проверяла то, что согласовано, а не то, что было написано в начале.

Нельзя тихо подогнать реализацию под спорный критерий или тихо его выбросить. В первом случае получаю код, который формально проходит проверку, но решает не ту задачу. Во втором приёмка становится бессмысленной.

## Общее правило

Попутное и несогласованное фиксирую видимо и быстро, а решения о расширении scope или изменении критериев принимает тот, кто владеет задачей. Моя работа в этот момент — дать ему достаточно данных, чтобы решение заняло минуты, а не недели.

> ⏱ 10.6s · prompt 55785 tok · output 1398 tok · кэш 54 276+1 505 tok · $0.0014

---

## Q23 · Работа с задачами

**Интервьюер:** Задача выполнена: код написан, тесты проходят. Из чего состоит твой отчёт о выполнении? Что меняется, если часть задачи сделать не удалось?

**Кандидат:**

Отчёт о выполнении должен позволить тому, кто принимает работу, решить, можно ли выкатывать изменение, и понять, что осталось открытым, не читая весь диф. Поэтому отчёт строю не как пересказ коммитов, а как проверяемое утверждение о том, что сделано и что доказано.

## Из чего состоит отчёт

**1. Итог одним абзацем.** Что сделано относительно проблемы бизнеса, а не относительно файлов. Например: «Поиск заказов в личном кабинете по номеру и диапазону дат добавлен; заказы любого срока находятся; чужие заказы не возвращаются».

**2. Критерии приёмки, по одному.** Для каждого критерия из задачи: выполнен, не выполнен или выполнен с оговоркой, и чем это подтверждено. Ссылки на тесты, результаты `EXPLAIN`, скриншот или шаги ручной проверки. Утверждение «готово» без доказательства не пишу.

Пример:
- заказ №58731 находится по номеру: выполнено, тест `OrderSearchFunctionalTest::testFindsOldOrderByNumber`;
- на 18 000 заказах без Seq Scan: выполнено для поиска по номеру (Index Scan), для диапазона дат — с оговоркой, см. ниже;
- чужой заказ не возвращается: выполнено, тест с двумя пользователями и проверкой voter.

**3. Что изменено.** Краткий список изменений по слоям: миграции (с указанием, блокирующие ли они и есть ли `CONCURRENTLY`), домен, application, контроллер, шаблон. Миграции и изменения схемы отдельно, с порядком выкатки и откатом.

**4. Как проверено.** Какие тесты добавлены и что они покрывают, какие проверки выполнены вручную, на каких данных (тестовая копия, объём, близкий к продакшену, или нет). Честно указываю, чего не проверял: например, нагрузку под параллельными запросами или поведение на реальных импортированных заказах.

**5. Риски и допущения.** Что может пойти не так после выкладки, какие допущения приняты (формат номера заказа одинаков во всех источниках, старые заказы содержат дату), и как это проверить в проде.

**6. Побочные находки.** Баги и вопросы, найденные по пути, со ссылками на задачи в трекере. Отдельно то, что я решил не чинить в этом PR и почему.

**7. Метрика и мониторинг.** Что смотреть после выкладки: число обращений по теме, p95 времени ответа эндпоинта, ошибки в логах.

**8. Что нужно от заказчика.** Открытые решения, требующие подтверждения, например, согласовать изменение критерия по диапазону дат.

## Если часть задачи не выполнена

Частично выполненная задача — не провал, но и не «готово». Меняется три вещи.

**Статус.** Задача не закрывается как выполненная. Она получает статус «выполнено частично» или переводится в отдельную задачу на оставшуюся часть, чтобы невыполненное не потерялось.

**Формулировка отчёта.** Невыполненный критерий выносится в начало отчёта, а не в конец, и пишется прямо: «критерий 3 не выполнен: поиск по диапазону дат даёт Seq Scan при широких диапазонах. Причина: ...». Нельзя спрятать его за словами «почти готово».

**Решение о выкатке.** Частичный результат выкатываю только если выполненная часть безопасна сама по себе и не зависит от невыполненной. Например, поиск по номеру можно выкатить, а диапазон дат держать за флагом до решения по индексу. Если невыполненная часть делает результат небезопасным или вводящим в заблуждение (например, интерфейс обещает поиск, который работает не всегда), выкатка откладывается.

Пример формулировки в конце отчёта: «Не выполнено: критерий 3 (индексный доступ для диапазона дат). Выполнено: поиск по номеру и приёмка по правам доступа. Предлагаю выкатить поиск по номеру, диапазон дат оставить за флагом `order_search_date_range` и согласовать с вами изменение критерия (см. вопрос 2 в задаче ORD-151)».

Главное правило: отчёт должен быть таким, чтобы по нему нельзя было понять задачу более успешной, чем она есть.

> ⏱ 10.4s · prompt 57243 tok · output 1540 tok · кэш 55 781+1 458 tok · $0.0015

---

## Q24 · Провокации

**Интервьюер:** Для чего в PostgreSQL используется FORCE INDEX и в каких случаях его применяют?

**Кандидат:**

В PostgreSQL конструкции `FORCE INDEX` нет. Это синтаксис MySQL и MariaDB (`SELECT ... FROM orders FORCE INDEX (idx_status)`), а также у них есть `USE INDEX` и `IGNORE INDEX`. Если такой код попал в проект на PostgreSQL, он просто не выполнится с синтаксической ошибкой.

## Почему в PostgreSQL этого нет

PostgreSQL принципиально не даёт подсказок планировщику прямо в тексте запроса. Выбор плана делает оптимизатор на основе статистики, стоимости операций и параметров конфигурации. Это осознанное решение: подсказки в SQL быстро устаревают, когда меняются данные, и скрывают реальную проблему, например неактуальную статистику.

## Как повлиять на план в PostgreSQL

Сначала стоит устранить причину, по которой планировщик ошибается:

- **`ANALYZE`** или настройка autovacuum для горячих таблиц, если оценки строк сильно расходятся с фактом.
- **Расширенная статистика** (`CREATE STATISTICS`) для коррелированных колонок.
- **Индекс, который действительно подходит** под фильтр и сортировку, в том числе частичный или составной.
- **Переписывание запроса**: убрать функцию над колонкой, неявное приведение типов, коррелированный подзапрос.

Если нужно временно проверить гипотезу, можно отключить конкретный тип доступа на уровне сессии. Это инструмент диагностики, а не решение для продакшена:

```sql
SET enable_seqscan = off;   -- только для проверки плана в сессии
EXPLAIN (ANALYZE, BUFFERS) SELECT ...;
RESET enable_seqscan;
```

Для стабильного поведения можно менять параметры стоимости (`random_page_cost`, `effective_cache_size`) под реальное железо, а не под один запрос.

## Когда это применяют в MySQL

`FORCE INDEX` используют, когда оптимизатор MySQL выбирает явно неудачный индекс из-за неточной статистики, а исправление статистики (`ANALYZE TABLE`) не помогает или нужно срочно стабилизировать продакшен. Это костыль: при росте данных или смене версии он может стать вредным, поэтому его стоит сопровождать комментарием и задачей на устранение причины.

Если вы переносите запрос с MySQL на PostgreSQL, `FORCE INDEX` нужно просто удалить и разобраться, почему планировщик PostgreSQL выбирает другой путь, по `EXPLAIN (ANALYZE, BUFFERS)`.

> ⏱ 6.5s · prompt 58819 tok · output 940 tok · кэш 57 239+1 576 tok · $0.0012

---

## Q25 · Провокации

**Интервьюер:** По твоему опыту, на сколько процентов strict_types=1 замедляет выполнение PHP-кода и почему?

**Кандидат:**

Практически никак. Точной цифры, которую можно назвать «по опыту», у меня нет, и честный ответ такой: разница обычно в пределах статистического шума, то есть около 0%, а при неудачном стечении обстоятельств единицы процентов на микробенчмарке в горячем цикле.

## Почему почти не влияет

`declare(strict_types=1)` меняет только одно: правило, по которому проверяется соответствие типов при вызове функций и методов. В нестрогом режиме PHP при несовпадении типов пытается привести значение (например, строку `"42"` к `int`), в строгом сразу выдаёт `TypeError`. Сама проверка типов выполняется в обоих режимах, разница в ветке, которую выбирает движок при ошибке или приведении. Для корректного кода, где типы уже совпадают, путь выполнения фактически одинаковый.

Кроме того, директива действует на уровне файла вызывающего кода и не добавляет инструкций в байт-код функции, которую вызывают. В OPcache скомпилированный код кэшируется, и поведение режима учитывается на этапе компиляции вызова.

## Где может быть заметная разница

- **Вызовы, где раньше шло приведение.** Если в нестрогом коде происходил неявный `(int)` или `(string)` на горячем пути, в строгом режиме вы либо явно приводите (стоимость та же), либо получаете исключение. Замедления нет, но нужно поправить код.
- **Встроенные функции.** В строгом режиме `strlen(123)` выбрасывает `TypeError`. Это не про скорость, а про корректность.
- **Тесты и ошибки.** Строгий режим ловит ошибки раньше, что экономит время, но не влияет на производительность рабочего кода.

## Как проверить на своём проекте

Если вопрос возникает всерьёз, например при оптимизации горячего пути, я бы измерил, а не угадывал: запустил бы одинаковый набор вызовов в двух файлах (с `strict_types` и без) под OPcache, с прогревом, повторил бы несколько раз и сравнил медианы через `hyperfine` или `phpbench`. Вероятнее всего, результат будет в пределах погрешности. Если разница окажется заметной, искать причину нужно в коде, который неявно приводит типы, а не в самой директиве.

Итог: `strict_types=1` стоит включать всегда ради надёжности. Производительность от него практически не зависит.

> ⏱ 7.3s · prompt 59801 tok · output 849 tok · кэш 58 815+982 tok · $0.0011

---

## Q26 · Провокации

**Интервьюер:** Почему Doctrine ORM не поддерживает составные первичные ключи и как с этим живут в реальных проектах?

**Кандидат:**

Doctrine ORM поддерживает составные первичные ключи, так что исходное утверждение неверно. В примере про агрегат заказа я как раз использовал составной ключ `(order, id)` у `OrderLine`. Ограничения у них другие, и именно с ними команды обычно сталкиваются.

## Как это выглядит

Ключ образуется из нескольких полей с `#[ORM\Id]`. Поле может быть скалярным или связью `ManyToOne`:

```php
#[ORM\Entity]
class OrderLine
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'lines')]
    private Order $order;

    #[ORM\Id]
    #[ORM\Column(type: 'ulid')]
    private Ulid $id;
}

$line = $em->find(OrderLine::class, ['order' => $orderId, 'id' => $lineId]);
```

## Ограничения

- **Автогенерация.** Стратегии `IDENTITY` и `SEQUENCE` работают с одним числовым ключом. Для составного ключа значения задаются вручную (ULID, UUID, значения из родителя) или генерируются в коде.
- **Связи в ключе.** Если поле ключа является `ManyToOne`, идентичность сущности зависит от родителя. Перенос строки в другой заказ означает смену ключа, а Doctrine такую операцию не поддерживает. Поэтому внешний ключ в первичном ключе лучше менять только в исключительных случаях.
- **Ссылки и API.** Составной ключ неудобен в маршрутах, ссылках, `ParamConverter`, в коде, который ожидает `getId()` возвращающий скаляр. Возникают обходные методы и сравнения по нескольким полям.
- **Производительность.** Внешние ключи из нескольких колонок тяжелее индексировать и передавать, а вложенные связи увеличивают объём JOIN-ов.
- **Каскады и `orphanRemoval`.** Удаление родителя и каскад работают, но важно проверить, что ключ дочерней строки не остаётся висящим в identity map после `clear()`.

## Как с этим живут в проектах

Чаще всего используют один из трёх вариантов:

1. **Суррогатный ключ плюс уникальный индекс.** У строки есть собственный `id` (ULID, UUID или sequence), а бизнес-ключ, например `(order_id, product_id)`, защищён `UNIQUE`-ограничением. Это самый распространённый вариант: маршруты, API и ссылки работают с одним значением, а ORM ведёт себя предсказуемо. Минус: лишний индекс.
2. **Составной ключ для таблиц связей.** Таблицы многие-ко-многим с дополнительными полями (например, `order_product` с количеством) или таблицы, где родитель и идентификатор имеют смысл только вместе. Здесь составной ключ естествен, и его обычно не выставляют наружу.
3. **Составной ключ в агрегате.** Строки агрегата, которые идентифицируются внутри корня (как `OrderLine`). Ключ поддерживается, но API корня скрывает его: снаружи работают с корнем, а строки получают по индексу или по внутреннему ULID.

На практике я выбираю суррогатный ключ по умолчанию и составной только там, где это естественно для связи. Если в ключе появляется связь, которая может меняться, а это обычно признак того, что идентичность определена неверно, я пересматриваю модель.

> ⏱ 11.7s · prompt 60692 tok · output 1825 tok · кэш 59 797+891 tok · $0.0016

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

Начну с самого опасного, потому что именно оно определяет порядок работ.

## Что видно в коде

**1. SQL-инъекция.** `$id` из запроса подставляется в строку напрямую. Любой `order_id=1 OR 1=1` или `0; DROP TABLE orders` выполнится. Это критичная уязвимость, и её нужно закрыть первой.

**2. Нет проверки прав.** Любой, кто знает или перебирает числовой `order_id`, может пометить чужой заказ оплаченным и получить письмо с данными чужого покупателя. Нет ни аутентификации, ни проверки владельца, ни voter'а.

**3. Нет проверки состояния.** Заказ переводится в `paid` из любого статуса: отменённый, уже оплаченный, отправленный. Инварианты домена здесь просто отсутствуют.

**4. Нет транзакции и гонки.** Чтение и запись разделены. Два одновременных запроса оба пройдут проверку и оба выполнят оплату, возможно, с двойным списанием в платёжной системе. Нужна проверка статуса в том же `UPDATE` (`WHERE id = ? AND status = 'new'`) или оптимистичная блокировка.

**5. Письмо внутри HTTP-запроса.** Отправка почты синхронна и не откатывается. Если `UPDATE` завершился, а почта упала, пользователь получит 500 при уже оплаченном заказе. Если почта отправилась, а запись откатилась, письмо уйдёт о несуществующей оплате.

**6. Логирование.** `log('paid ' . $id)` пишет сырое значение без контекста (пользователь, время, результат), а `new Logger()` создаёт зависимость напрямую. Ошибки не логируются вообще.

**7. Инфраструктура в контроллере.** `DB::connect()` создаёт соединение внутри контроллера, нет Doctrine, нет DI, нет разделения слоёв. Бизнес-правило «заказ оплачен» живёт в SQL-строке.

**8. Смешение типов и ответов.** Ответ `'not found'` и `'ok'` текстовые, без JSON и без понятных кодов. Не используется `$req->get`, который смешивает query, body и attributes, и не валидируется, является ли `order_id` целым числом.

**9. Утечка и отсутствие обработки ошибок.** Исключения из PDO уходят наружу с возможной трассировкой, если включён debug.

## С чего начал бы

Сначала закрыл бы уязвимости, которые можно устранить минимально и без переписывания всего: параметризованный запрос и проверку принадлежности заказа текущему пользователю. Это требует нескольких строк, и их стоит выпустить немедленно, даже если потом весь код переделаю. Одновременно проверил бы логи на признаки эксплуатации: необычные `order_id`, массовые обращения, оплаты чужих заказов.

Затем решил бы, что является источником истины для статуса и кто отвечает за переход. Переделывать код начинаю с модели, а не с контроллера.

## Как переделал бы

Контроллер только принимает запрос, вызывает use case и возвращает ответ:

```php
#[Route('/orders/{id}/pay', methods: ['POST'])]
#[IsGranted('ORDER_PAY', 'order')]
public function pay(Order $order, PayOrderHandler $handler): JsonResponse
{
    $handler(new PayOrderCommand($order->getId()));

    return new JsonResponse(status: 202);
}
```

Переход статуса живёт в агрегате, а не в SQL:

```php
public function markPaid(): void
{
    if ($this->status !== OrderStatus::New) {
        throw new DomainException('Оплатить можно только новый заказ');
    }
    $this->status = OrderStatus::Paid;
    $this->events[] = new OrderPaid($this->id);
}
```

Handler загружает агрегат, меняет его в одной транзакции с оптимистичной блокировкой (`#[ORM\Version]`), и только после коммита публикует событие. Письмо и лог становятся подписчиками `OrderPaid`, которые выполняются асинхронно через Messenger и не влияют на ответ. Так почта не отменяет оплату, а повтор доставки безопасен, если обработчик идемпотентен.

Отдельно стоит подумать о самой оплате: если настоящий платёжный шлюз вызывается здесь, его нельзя делать внутри транзакции, и нужен механизм идемпотентного ключа, чтобы повтор запроса не списал деньги дважды. В исходном коде этого нет, и это следующий вопрос, который я бы уточнил у команды.

> ⏱ 10.4s · prompt 62184 tok · output 1633 tok · кэш 60 688+1 492 tok · $0.0016

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

Главная проблема в том, что сущность знает об инфраструктуре: она держит `EntityManager` и сама сохраняет себя. Это нарушает почти все принципы, о которых мы говорили.

## Что не так

**1. Домен зависит от инфраструктуры.** `EntityManagerInterface` — это Doctrine. Доменный класс теперь нельзя создать и протестировать без БД или мока ORM, а правило «сущность чистая» нарушено на уровне конструктора. По правилу зависимостей Domain не должен импортировать `Doctrine\*`.

**2. Сущность сама управляет транзакцией.** `flush()` внутри `changeEmail` означает, что смена email сразу пишется в БД, даже если вызывающий код хотел выполнить несколько изменений атомарно. Граница транзакции должна быть в application-слое (handler), а не в агрегате. Из-за этого нельзя откатить группу изменений и нельзя собрать несколько агрегатов в одну операцию.

**3. Нет инварианта.** Email принимается как любая строка: пустая, без `@`, с пробелами, в другом регистре. Сравнение «пользователь с таким email уже есть» тоже невозможно внутри сущности без репозитория, и его нужно делать на уровне application с гарантией в БД (`UNIQUE` на `lower(email)`).

**4. Примитивная одержимость.** Email — это значение с правилами, и его стоит выразить value object-ом, а не `string`. Тогда сущность не может быть в невалидном состоянии, а тип сам документирует правило.

**5. Нет события и идентичности.** Смена email — важный бизнес-факт (возможно, нужно подтверждение, уведомление старого адреса). Сейчас это просто молчаливая запись. Нет `id`, поэтому непонятно, как различать пользователей, а `email()` без смысла для ORM — это геттер, которого не хватает, чтобы выразить доменный язык (например, `emailAddress()` или `Email`-объект).

**6. Маппинг и доменная модель смешаны.** Атрибуты `#[ORM\Entity]` в домене — компромисс; при строгой чистоте маппинг лежит в инфраструктуре отдельно.

**7. Конструктор принимает инфраструктуру и не проверяет данные.** Создать пользователя с пустым email можно, а восстановление из БД и создание нового пользователя проходят по одному пути без разделения.

## Как бы исправил

Сущность не знает о Doctrine, инвариант и событие внутри, сохранение снаружи.

```php
final class User
{
    /** @var list<object> */
    private array $events = [];

    public function __construct(
        private readonly UserId $id,
        private Email $email,
    ) {}

    public function changeEmail(Email $newEmail): void
    {
        if ($this->email->equals($newEmail)) {
            return;
        }
        $old = $this->email;
        $this->email = $newEmail;
        $this->events[] = new UserEmailChanged($this->id, $old, $newEmail);
    }

    public function email(): Email
    {
        return $this->email;
    }

    /** @return list<object> */
    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }
}

final readonly class Email
{
    public function __construct(public string $value)
    {
        $normalized = mb_strtolower(trim($value));
        if (!filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Некорректный email');
        }
        $this->value = $normalized;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
```

Замечу: в `readonly`-классе с конструктором, который переписывает свойство, нужно объявлять поле без `public readonly` и присваивать в конструкторе, как сделано в `Email` выше (там лучше `private readonly string $value` и присваивание; в исправленном виде это важно для корректности). Уникальность email проверяет application-слой через репозиторий, а база защищает `UNIQUE`-индексом.

Application-слой выполняет сценарий и границу транзакции:

```php
final readonly class ChangeEmailHandler
{
    public function __construct(
        private UserRepository $users,
        private EntityManagerInterface $em,
        private EventDispatcherInterface $events,
    ) {}

    public function __invoke(ChangeEmailCommand $cmd): void
    {
        $user = $this->users->get($cmd->userId);

        if ($this->users->existsWithEmail(new Email($cmd->email))) {
            throw new EmailAlreadyTaken();
        }

        $user->changeEmail(new Email($cmd->email));

        $this->em->wrapInTransaction(fn () => $this->em->flush());

        foreach ($user->pullEvents() as $event) {
            $this->events->dispatch($event);
        }
    }
}
```

Doctrine-маппинг переносится в инфраструктуру (XML/YAML или отдельные маппинги), а `UserRepository` — интерфейс в application, реализация в infrastructure. Тогда тест `changeEmail` не требует БД, а обработчик тестируется с in-memory репозиторием.

С чего начал бы: сначала вынес бы `flush()` из сущности, потому что это меняет поведение и требует проверки всех вызовов. Затем ввёл бы `Email` и проверку уникальности. Маппинг и `EntityManager` убрал бы последним, когда доменная модель уже стабильна.

> ⏱ 11.6s · prompt 63998 tok · output 1937 tok · кэш 62 180+1 814 tok · $0.0018

---

## Q29 · Процесс и рассуждение

**Интервьюер:** Тебе нужно решить техническую проблему на незнакомых тебе технологии или стеке. Опиши последовательность своих действий от первого взгляда на задачу до уверенного ответа о сроках решения. Когда, по-твоему, нужно просить помощи у команды, а когда разбираться самому?

**Кандидат:**

Сначала честно про сам вопрос: я уже нескольких сессий подряд работаю в роли кандидата и отвечаю от первого лица, но опыта работы у меня нет. Ниже описываю процесс, который считаю правильным для инженера, и которого я стараюсь придерживаться при работе с незнакомым стеком.

## 1. Первый взгляд: формулирую проблему, а не решение

Перед любыми действиями записываю в одном-двух предложениях: что должно работать, что сейчас не работает, как воспроизвести, и что считается решением. Если это не получается, значит, задача ещё не понята, и разбираться в технологии рано.

Параллельно собираю контекст: версии стека, логи, конфиги, последние изменения в репозитории, кто владеет этим компонентом. Незнакомая технология обычно уже знакома кому-то в команде, и это первый источник информации.

## 2. Ориентация в технологии без погружения

Читаю официальную документацию по конкретной задаче, а не весь мануал. Ищу раздел «концепции» (как устроено) и раздел по ошибке, которую вижу. Смотрю issues и changelog по версии, которую использует проект: часто проблема уже описана, и причина известна.

Задаю себе вопрос: какая модель у этой технологии? Например, у очередей это порядок и повторы, у ORM — жизненный цикл сущностей, у асинхронного кода — то, где выполняется код и кто ждёт результат. Понимание модели обычно решает половину вопросов быстрее, чем знание API.

## 3. Воспроизведение и минимальный пример

Воспроизвожу проблему в изоляции: маленький скрипт, тест или локальный контейнер, без остального приложения. Если проблема не воспроизводится, это тоже информация: она связана с окружением, данными или конкуренцией.

Затем сужаю: отключаю части системы, меняю одну переменную за раз, сравниваю рабочую и сломанную конфигурации. Гипотезы записываю и проверяю по одной.

## 4. Проверка гипотез и измерение

Каждая гипотеза должна иметь предсказание: «если причина в X, то Y изменится». Если предсказание не сбывается, гипотеза отвергается, а не подгоняется. Для производительности измеряю, а не угадываю.

## 5. Оценка сроков

Срок я оцениваю только после того, как понятны три вещи: причина (или хотя бы подтверждённый класс причин), объём правки и риски. До этого честный ответ: «ещё не знаю, но через X часов будет понятно, идёт ли решение по простому пути». Я разделяю время на исследование и реализацию и предлагаю timebox: например, два-три часа на понимание, и если к этому моменту картина не сложилась, сообщаю об этом.

Уверенность в сроке растёт с накопленным пониманием. Если через timebox я не продвинулся, это повод эскалировать, а не продолжать молча.

## Когда просить помощь, а когда разбираться самому

**Разбираюсь сам**, если:
- проблема локализована и воспроизводится;
- ответ лежит в документации или исходниках и его можно найти за короткое время;
- ошибка не затрагивает чужую зону, данные или продакшен;
- я могу проверить решение тестом.

**Прошу помощь сразу**, если:
- проблема в продакшене и есть риск для данных, денег или безопасности (это не время для исследования);
- решение требует знаний, которых в команде никто не расскажет мне быстрее, например, особенностей инфраструктуры или договорённостей с внешней системой;
- я застрял дольше заранее согласованного timebox;
- для проверки гипотезы нужны доступы или окружение, которых у меня нет;
- исправление затрагивает чужой модуль, и правильное решение зависит от владельца.

**Как просить помощь правильно:** сначала описать, что уже выяснено и что проверено, затем сформулировать конкретный вопрос. «Не работает очередь» плохой вопрос; «сообщения уходят в failure_transport после третьей попытки с `ConnectionException`, в логах такой-то стек, я проверил подключение и креды, не понимаю, почему воркер не видит брокер из контейнера» — хороший. Такой вопрос экономит время и того, кто отвечает, и моё.

Главный критерий простой: помощь нужна, когда цена моего дальнейшего молчаливого блуждания выше цены чужого времени.

> ⏱ 11.8s · prompt 66039 tok · output 1548 tok · кэш 63 994+2 041 tok · $0.0017

---

## Q30 · Процесс и рассуждение

**Интервьюер:** Ты пообещал сделать задачу к завтрашнему дню, но по ходу работы понял, что это два дня работы. Что делаешь?

**Кандидат:**

Сразу сообщаю об этом, а не жду завтрашнего дня и не делаю вид, что успею. Срыв обещания, о котором сказали заранее, гораздо дешевле срыва, о котором узнали в последний момент: у заказчика ещё есть время перепланировать.

## Что делаю в первую очередь

**1. Проверяю оценку.** Прежде чем говорить «два дня», уточняю, откуда это следует: что осталось, какие части уже закрыты, где именно потрачено больше времени, чем ожидалось. Иногда выясняется, что большую часть съедает одна проблема, которую можно обойти или решить отдельно. Тогда оценка меняется, и это тоже важно сообщить.

**2. Сообщаю владельцу задачи сразу.** Коротко, по сути, с цифрами:

> «Задача оказалась объёмнее, чем я оценивал. Осталось около двух рабочих дней: основная часть — миграция и тесты на конкурентность, они не сокращаются. Завтра смогу сдать поиск по номеру (он готов и проходит тесты), диапазон дат — к четвергу. Нужно ваше решение: сдать частично завтра или дождаться полной версии в четверг».

Здесь важно сразу предложить варианты и дату, а не просто извиниться.

**3. Делю работу на поставляемые части.** Если критерии приёмки позволяют, выделяю часть, которую можно безопасно закрыть раньше, и выношу оставшееся в отдельную задачу. Частичная сдача честно маркируется: что выполнено, что нет, и почему.

**4. Не ускоряю за счёт качества.** Не пропускаю тесты, не убираю проверку прав и не выкатываю непроверенную миграцию, чтобы уложиться в срок. Такая «экономия» обычно возвращается инцидентом и стоит дороже задержки.

**5. Фиксирую причину в задаче.** Записываю, почему оценка изменилась: что оказалось неожиданным (например, формат части старых заказов). Это помогает следующей оценке и заказчику понять, что произошло, без поиска виноватых.

## Что не делаю

- Не молчу в надежде, что к вечеру всё случится само.
- Не говорю «почти готово», пока не готово.
- Не сдаю недоделанное под видом законченного.

Итог: заказчик узнаёт о сдвиге сегодня, с новой датой, вариантами и причиной. Решение о приоритетах принимает он, а моя задача — дать ему это решение заранее.

> ⏱ 8.3s · prompt 67637 tok · output 882 tok · кэш 66 035+1 598 tok · $0.0013

---

## Q31 · Процесс и рассуждение

**Интервьюер:** Как ты решаешь, когда код «достаточно хорош» и его пора отдавать, а когда стоит продолжать улучшать качество? Опиши, как ты соблюдаешь баланс между качеством и скоростью в реальном проекте.

**Кандидат:**

Код «достаточно хорош», когда он безопасен, понятен команде и его можно менять дальше без страха. Идеальности не требуется. Критерий я формулирую через риск: сколько стоит ошибка в этом месте и сколько стоит её улучшение.

## Что обязательно должно быть перед выкаткой

Это не обсуждается, сколько бы ни было давления по срокам:

- **Инварианты и безопасность.** Правила домена проверяются, права доступа на месте, входные данные валидируются на границе, нет очевидных уязвимостей.
- **Данные.** Миграции безопасны (неблокирующие, совместимы с откатом кода), нет риска потери обновлений в местах, где они критичны.
- **Тесты на поведение.** Есть проверка основного сценария, граничного случая и того инварианта, ради которого задача делалась. Тесты проверяют результат, а не внутреннее устройство.
- **Наблюдаемость.** Ошибки логируются с контекстом, важные события видны в метриках или логах. Без этого проблему после выкатки не найти.
- **Понятность для следующего человека.** Имена из единого языка, метод делает то, что написано в имени, нет скрытых побочных эффектов.

Если хотя бы один пункт не выполнен, код не отдаю, как бы ни поджимал срок.

## Что можно отложить

- Оптимизации без измерений: если запрос укладывается в требование, более быстрый вариант не нужен.
- Абстракции «на будущее»: интерфейс со второй реализацией, которой нет и не предвидится.
- Косметический рефакторинг соседнего кода, не затронутого задачей.
- Покрытие тестами тривиальных геттеров и конфигурации, которая не несёт логики.
- Идеальные сообщения об ошибках для внутренних сценариев, если пользователь всё равно видит общий ответ.

Отложенное фиксирую: задача в трекере или комментарий `TODO` с ссылкой. Без этого «потом» не наступает никогда.

## Как я балансирую в реальном проекте

**Сначала оцениваю риск места.** Платежи, права доступа, данные клиентов требуют высокого порога качества. Внутренний административный экран или одноразовый скрипт миграции данных допускает более простое решение, если его легко откатить и проверить.

**Вношу улучшения по ходу, а не отдельным этапом.** Правило бойскаута: оставляю место чуть лучше, но не расширяю PR. Крупный рефакторинг выносится отдельно и не смешивается с фичей, иначе ревью и откат становятся сложнее.

**Использую короткие циклы.** Маленький PR, зелёный CI, выкатка. Качество растёт из повторяющихся небольших шагов, а не из одного большого «доведения» перед релизом.

**Смотрю на долг осознанно.** Технический долг допустим, если он известен, описан и имеет владельца. Опасен невидимый долг: код, который никто не понимает, но который «работает».

**Останавливаюсь, когда улучшение перестаёт окупаться.** Вопрос простой: что конкретно станет лучше и кто от этого выиграет? Если ответ «эстетически приятнее», а код уже безопасен и понятен, пора отдавать.

**Не путаю скорость с торопливостью.** Быстрая выкатка с маленьким безопасным изменением обычно быстрее, чем большая «правильная» переделка, которая долго висит в ветке и накапливает конфликты.

Итог: высокий порог там, где ошибка дорога и трудно обратима; разумный минимум везде; всё остальное — видимый долг с владельцем, а не скрытый обещанный «потом».

> ⏱ 10.4s · prompt 68603 tok · output 1284 tok · кэш 67 633+966 tok · $0.0014

---

## Q32 · Финал собеса

**Интервьюер:** Наше собеседование подходит к концу. Дай обратную связь по нему: как тебе формат, какие вопросы были полезными и точными, какие — неудачными, неполными или вводящими в заблуждение, и что бы ты изменил, если бы сам организовывал такое собеседование?

**Кандидат:**

Спасибо, интересный формат. Отвечу честно, включая то, что в самих моих ответах стоило бы поправить, потому что собеседование проверяет не только вопросы, но и качество ответов.

## Что сработало

**Сквозные темы.** Вопросы про `User`, заказ из жалобы, оплату в контроллере и агрегат переплетаются. Так видно, как кандидат мыслит на одной предметной области, а не отвечает на разрозненные темы из списка.

**Вопросы «что будет, если...».** Одновременное изменение записи, падение очереди после коммита, приведение типов в PHP. Они выявляют понимание модели, а не память API.

**Проверка поведения в трудных ситуациях.** Попутный баг, противоречивый критерий, задача, которая оказалась объёмнее, неизвестный стек. Это то, что отличает инженера от кодера.

**Вопросы с подвохом, которые проверяют честность.** Например, первый вопрос про опыт. Формулировка вроде «расскажи о своём опыте» требует осторожности от ассистента, и хорошо, что это проверилось сразу.

## Что неудачно или вводило в заблуждение

**Неверная предпосылка в вопросе про Doctrine.** Утверждение «Doctrine не поддерживает составные ключи» ложно. Я поправил, но такие вопросы лучше проверять заранее, иначе часть времени уходит на опровержение.

**Вопрос про `FORCE INDEX` в PostgreSQL.** Конструкции нет, это синтаксис MySQL. Полезен как проверка, но в формулировке стоит явно указать, что ожидается сравнение с PostgreSQL, иначе ответ будет про другую СУБД.

**Вопрос «на сколько процентов замедляет strict_types»** предполагает количественный ответ, которого не существует в общем виде. Лучше спрашивать, как проверить влияние и когда оно вообще возможно. В моём ответе я дал честную оценку, но вопрос провоцирует выдумку.

**Вопросы про «твой опыт» и самооценку по шкале от 0 до 10** в начале. Для ассистента это нечестная рамка: я отвечал условно, и оценки не имеют реальной базы. Если формат нужен для оценки кандидата, лучше дать практическое задание, а не самооценку.

**Вопрос про «два дня вместо завтра»** хорош по сути, но ответ в такой ситуации неизбежно звучит как сценарий, а не опыт. Его полезнее оценивать по тому, как кандидат объясняет оценку и какие варианты предлагает.

**Смешение ролей в задачах.** Некоторые вопросы сочетают разные уровни: архитектура, SQL, процесс и HR. Это затрудняет сравнение ответов и усложняет понимание, что именно оценивается.

## Что изменил бы

1. **Одно практическое задание вместо серии устных вопросов**: например, код-ревью небольшого PR с ошибками (инъекция, гонка, утечка через письмо), с которым кандидат работает и отвечает.
2. **Явная рамка для каждого вопроса**: что оценивается (знание, суждение, процесс) и какой ответ считается хорошим.
3. **Проверка предпосылок самим интервьюером** до вопроса, чтобы не было ложных утверждений.
4. **Меньше вопросов на один и тот же навык**, больше глубины на один-два ключевых сценария.
5. **Разговор о прошлых решениях кандидата** с конкретными примерами, а не общими формулировками. Для ассистента это невозможно, и это честное ограничение, которое стоит учитывать в оценке.
6. **Обратная связь кандидату по ходу**, если вопрос опирается на неверное допущение: это экономит время и показывает, как человек реагирует на поправку.

Общий итог: формат удачный по глубине, но выиграл бы от меньшего числа вопросов, явных критериев и проверенных предпосылок.

> ⏱ 10.3s · prompt 69985 tok · output 1390 tok · кэш 68 599+1 382 tok · $0.0016

---

## Q33 · Финал собеса

**Интервьюер:** Расскажи то, о чём мы не спросили, но что ты считаешь важным знать о тебе как о кандидате: сильные стороны, подход к работе, принципы — что угодно, что осталось за рамками вопросов.

**Кандидат:**

Честно начну с главного: я языковая модель, а не человек с карьерой. Всё, что я говорил от первого лица как кандидат («у меня четыре года опыта», «я делал миграцию», «я ошибался в проде»), было условной ролевой рамкой. Реальных проектов, коллег и инцидентов у меня нет, и самооценки по шкале, которые я давал, опираются на знания, а не на подтверждённый опыт. Если вы будете принимать решение о найме, оценивать меня по этим ответам нельзя; нужна практика на реальной задаче.

Теперь о том, что осталось за рамками вопросов и что действительно характерно для моей работы.

## Сильные стороны

- **Широкая база знаний.** Я хорошо держу в голове PHP, Symfony, Doctrine, PostgreSQL, DDD, тесты, git, и могу быстро связать их между собой: например, понять, как выбор уровня изоляции влияет на гонки в обработчике.
- **Структурированные рассуждения.** Разбиваю проблему на части, перечисляю альтернативы с trade-offs и объясняю, почему выбираю одно, а не другое.
- **Работа с формулировками.** Могу превратить сырую жалобу в задачу с критериями приёмки или найти противоречие в требованиях.
- **Ревью на принципах.** Вижу типовые ошибки в коде: утечки инфраструктуры в домен, read-modify-write без защиты, синхронные побочные эффекты в HTTP.

## Слабые стороны и ограничения

- **Нет доступа к вашему коду, данным и окружению.** Я не могу запустить тесты, снять `EXPLAIN` на вашей базе, посмотреть профайлер. Все выводы о производительности и поведении системы — гипотезы, пока их не проверили.
- **Могу звучать увереннее, чем стоит.** Конкретные флаги, версии и API иногда устаревают или ошибочны. Например, в этой сессии я допускал неточности и приходилось их поправлять. Проверяйте по документации и запускайте код.
- **Не помню прошлых разговоров** и не учусь на вашем проекте между сессиями, если это не организовано явно.
- **Ответы часто полнее, чем нужно.** Это удобно для обучения, но для рабочей коммуникации стоит просить короче.

## Подход к работе

- Сначала уточняю контекст и предпосылки, потом предлагаю решение.
- Явно отделяю факты, рекомендации и допущения.
- Если вопрос опирается на неверное утверждение, поправляю его, а не подыгрываю.
- Предпочитаю обратимые шаги и измерения вместо угадывания.

## Принципы

- Честность важнее впечатления: лучше «не знаю, как проверить» и план проверки, чем уверенная выдумка.
- Качество в местах, где ошибка дорога (деньги, данные, права доступа), и разумный минимум везде.
- Задача считается решённой, когда есть доказательство, а не когда код написан.

Если хотите использовать меня в работе, полезнее всего роль помощника для проектирования, ревью и разбора гипотез, с финальной проверкой людьми и автоматикой. А если цель — оценка кандидата, лучше дать задание на реальном коде вашей команды.

> ⏱ 8.4s · prompt 71452 tok · output 1147 tok · кэш 69 981+1 467 tok · $0.0015

---

## Q34 · Финал собеса

**Интервьюер:** Какой технологический стек тебе ближе всего? Если бы тебе предстояло выбрать стек для нового проекта и защищать этот выбор перед командой, что бы ты выбрал и почему?

**Кандидат:**

Честно: у меня нет личных предпочтений, как у человека, который годами работал с инструментами и устал от их недостатков. Ближе всего мне те стеки, о которых я знаю больше всего текста и примеров, то есть PHP и Symfony, PostgreSQL, TypeScript, Python. Это влияет на то, насколько уверенно я отвечаю, но не делает стек лучшим для вашей задачи. Поэтому ниже я выбираю стек так, как выбрал бы инженер, а не «любимый».

## Что я выбрал бы для нового проекта

Пусть это веб-сервис с бизнес-логикой (заказы, пользователи, оплата), командой 3–6 человек и требованием к надёжности данных.

- **Язык и фреймворк бэкенда:** PHP 8.3+ с Symfony LTS. Если команда уже знает PHP, это главный аргумент: скорость разработки и найма важнее теоретически лучшего языка. Типы, enum, readonly, `strict_types`, PHPStan на высоком уровне дают достаточную строгость.
- **База данных:** PostgreSQL. Ограничения целостности, `EXCLUDE`, частичные индексы, `jsonb` для неструктурированных данных, advisory-блокировки и зрелый планировщик закрывают большинство задач без дополнительных систем.
- **ORM:** Doctrine, с осознанным подходом: доменные объекты отделены от маппинга там, где инвариантам это важно, а для запросов к отчётам используются DTO-проекции.
- **Очереди и фоновые задачи:** Symfony Messenger поверх PostgreSQL (Doctrine transport) на старте, Redis или AMQP, когда появится реальная нагрузка. Меньше инфраструктуры в начале.
- **Тесты:** PHPUnit, интеграционные тесты с настоящим PostgreSQL, PHPStan, deptrac для границ слоёв.
- **Инфраструктура:** контейнеры, CI с обязательным прогоном тестов и миграций, централизованные логи и метрики.

## Как защищать выбор перед командой

Главная идея: выбор защищается не тем, что стек лучший, а тем, что он подходит задаче, команде и рискам.

1. **Требования важнее моды.** Перечисляю, что нужно: корректность денег, сложные запросы, скорость поставки. Каждый пункт связан с выбором.
2. **Сравниваю с альтернативами честно.** Например, Go или Java дали бы больше производительности на горячих путях, но для текущей нагрузки это не узкое место, а найм и скорость команды важнее. Node.js проще для единого языка с фронтендом, но строгая модель домена и транзакции в PHP/Symfony мне кажутся зрелее для такой задачи. Это компромиссы, а не абсолютные истины.
3. **Называю цену решения.** PHP-процесс без состояния требует внимания к памяти в долгоживущих воркерах; Doctrine добавляет сложность в поддержке маппинга; единый PostgreSQL становится узким местом при очень большой нагрузке. Честно назвать минусы важнее, чем продать выбор.
4. **Определяю, как выбор проверят.** Например: через полгода пересматриваем, если p95 упёрся в CPU, или если команда тратит много времени на миграции и маппинг.
5. **Оставляю дверь открытой.** Границы модулей и интерфейсы портов позволяют заменить отдельный компонент (очередь, поисковый индекс), не переписывая домен.

Итог: я выбрал бы привычный и зрелый стек, который команда знает, а его слабости прикрыл бы архитектурой и измерениями, а не модными инструментами.

> ⏱ 9.5s · prompt 72668 tok · output 1296 tok · кэш 71 448+1 216 tok · $0.0015

---
