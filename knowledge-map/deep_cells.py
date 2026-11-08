#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Клетки зоны «Глубина» (L9/L10) карты знаний: 26 базовых тем × 2 уровня.

Это данные для build_deep_cells.py (генерирует questions-deep{,-en,-zh}.json)
и verify_deep.py (батарея машинной верификации эталонов).

Каждая клетка:
    id, level    — T<тема>-L<9|10>
    note         — короткое пояснение ловушки (ru/en/zh)
    prompt       — текст запроса (ru/en/zh); код один и тот же во всех языках
    grader       — php-эталон (интерпретатор 8.5 считает сам) или accept/regex
                   (эталон заморожен после машинной верификации: vendor,
                   docker psql, curl-заглушка, deptrac, bootstrap.css)
    exp          — проверенный эталонный вывод (контроль для батареи)

Уровни: L9 — «опытный PHP-разработчик стабильно прав» (цель: 27B 50–75%),
L10 — «береговая линия: ошибаются и сильные» (цель: 40–60% у 27B).
"""

T01_L9 = """echo intdiv(7, 2) . intdiv(-7, 2);"""

T01_L10 = """echo -3 ** 2;"""

T02_L9 = """echo '1e1' == '10' ? 'true' : 'false';"""

T02_L10 = """switch ('a') {
    case 0: echo 'zero'; break;
    default: echo 'other';
}"""

T03_L9 = """echo strtr('abc', ['a' => '1', 'ab' => '2']);"""

T03_L10 = """echo '[' . substr('hello', -10) . ']';"""

T04_L9 = """$a = ['k' => 1, 'n' => 2];
$b = ['k' => 9, 'm' => 3];
echo implode(',', array_keys($a + $b));"""

T04_L10 = """$a = [[1, 'x', 2], [2, 'y', 1], [3, 'z', 2]];
usort($a, fn($p, $q) => $p[2] <=> $q[2]);
echo implode('', array_column($a, 1));"""

T05_L9 = """function f(int $a, int $b): int
{
    return $a - $b;
}
echo f(...['b' => 1, 'a' => 5]);"""

T05_L10 = """class P
{
    private int $v = 3;
    public function g(): int { return $this->v; }
}
$p = new P;
$f = $p->g(...);
$p = null;
echo $f();"""

T06_L9 = """class A
{
    public function who(): string { return static::class; }
}
class B extends A {}
echo (new B)->who();"""

T06_L10 = """class C
{
    public int $x = 0 {
        set { $this->x = $value * 2; }
    }
}
$c = new C;
$c->x = 5;
echo $c->x;"""

T07_L9 = """trait TA
{
    public function f(): string { return 'A'; }
}
trait TB
{
    public function f(): string { return 'B'; }
}
class TC
{
    use TA, TB { TB::f insteadof TA; TA::f as g; }
}
echo (new TC)->f() . (new TC)->g();"""

T07_L10 = """trait Counter
{
    public static int $n = 0;
}
class CX { use Counter; }
class CY { use Counter; }
CX::$n = 1;
echo CY::$n;"""

T08_L9 = """function f(): int
{
    try { return 1; } finally { return 2; }
}
echo f();"""

T08_L10 = """try {
    try {
        throw new RuntimeException('first');
    } finally {
        throw new LogicException('second');
    }
} catch (LogicException $e) {
    echo $e->getMessage();
}"""

T09_L9 = """namespace N;
use App\\Foo as Baz;
echo Baz::class . '|' . \\Baz::class;"""

T09_L10 = """namespace Ns;
class Foo {}
$c = 'Foo';
echo (class_exists($c) ? 'yes' : 'no')
   . (class_exists(__NAMESPACE__ . '\\\\' . $c) ? 'yes' : 'no');"""

T10_L9 = """$a = [1, 2, 3];
foreach ($a as &$v) {}
$v = 99;
echo implode(',', $a);"""

T10_L10 = """class A
{
    public function inc(): int
    {
        static $n = 0;
        return ++$n;
    }
}
class B extends A {}
$a = new A;
$b = new B;
$a->inc();
$b->inc();
echo $a->inc();"""

T11_L9 = """function g(): Generator
{
    $x = yield 1;
    return $x + 1;
}
$g = g();
$g->send(10);
echo $g->getReturn();"""

T11_L10 = """function inner(): Generator
{
    yield 1;
    yield 2;
    return 'ok';
}
function outer(): Generator
{
    $r = yield from inner();
    yield $r;
}
echo implode(',', iterator_to_array(outer()));"""

T12_L9 = """echo base64_encode('a:b');"""

T12_L10 = """error_reporting(E_ALL & ~E_NOTICE);
echo error_reporting();"""

T13_L9 = """use Symfony\\Component\\HttpFoundation\\Request;
$r = Request::create('/demo', 'GET', [], [], [], ['HTTP_X_TOKEN' => 'abc']);
echo $r->headers->get('X-Token');"""

T13_L10 = """use Symfony\\Component\\EventDispatcher\\EventDispatcher;
use Symfony\\Contracts\\EventDispatcher\\Event;
$d = new EventDispatcher();
$out = '';
$d->addListener('e', function () use (&$out) { $out .= 'B'; }, 10);
$d->addListener('e', function () use (&$out) { $out .= 'A'; });
$d->addListener('e', function () use (&$out) { $out .= 'C'; }, 5);
$d->dispatch(new Event(), 'e');
echo $out;"""

T16_L9 = """use Symfony\\Component\\Messenger\\MessageBus;
use Symfony\\Component\\Messenger\\Middleware\\MiddlewareInterface;
use Symfony\\Component\\Messenger\\Middleware\\StackInterface;
use Symfony\\Component\\Messenger\\Envelope;

final class Trace implements MiddlewareInterface
{
    public function __construct(private string $tag) {}
    public function handle(Envelope $e, StackInterface $stack): Envelope
    {
        echo $this->tag;
        return $stack->next()->handle($e, $stack);
    }
}
$bus = new MessageBus([new Trace('M1'), new Trace('M2')]);
$bus->dispatch(new Envelope(new stdClass()));"""

T16_L10 = """use Symfony\\Component\\Messenger\\MessageBus;
use Symfony\\Component\\Messenger\\Middleware\\HandleMessageMiddleware;
use Symfony\\Component\\Messenger\\Handler\\HandlersLocator;
use Symfony\\Component\\Messenger\\Handler\\HandlerDescriptor;
use Symfony\\Component\\Messenger\\Envelope;

class Ping {}
$n = 0;
$d1 = new HandlerDescriptor(function (Ping $m) use (&$n) { $n += 1; }, ['alias' => 'h1']);
$d2 = new HandlerDescriptor(function (Ping $m) use (&$n) { $n += 10; }, ['alias' => 'h2']);
$bus = new MessageBus([
    new HandleMessageMiddleware(new HandlersLocator([Ping::class => [$d1, $d2]])),
]);
$bus->dispatch(new Envelope(new Ping()));
echo $n;"""

T18_L9 = """use PHPUnit\\Framework\\TestCase;
use PHPUnit\\Framework\\Attributes\\DataProvider;

final class CalcTest extends TestCase
{
    public static function prov(): array
    {
        return [[1], [2], [3]];
    }

    #[DataProvider('prov')]
    public function test_sum(int $x): void
    {
        $this->assertTrue($x > 0);
    }

    #[DataProvider('prov')]
    public function test_sum_abs(int $x): void
    {
        $this->assertTrue($x > 0);
    }

    public function test_other(): void
    {
        $this->assertTrue(true);
    }
}"""

T18_L10 = """use PHPUnit\\Framework\\TestCase;
use PHPUnit\\Framework\\Attributes\\DataProvider;

final class Calc2Test extends TestCase
{
    public function prov(): array
    {
        return [[1], [2]];
    }

    #[DataProvider('prov')]
    public function test_x(int $x): void
    {
        $this->assertTrue($x > 0);
    }
}"""

T21_L9 = """{{ false|default('c') }}"""

T21_L10 = """{{ 'a' ~ 1 + 2 }}"""

T22_L9 = """use Symfony\\Component\\Validator\\Validation;
use Symfony\\Component\\Validator\\Constraints as Assert;
$v = Validation::createValidator();
echo count($v->validate([''], [new Assert\\NotBlank()]));"""

T22_L10 = """use Symfony\\Component\\Form\\Forms;
use Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType;
$factory = Forms::createFormFactoryBuilder()->getFormFactory();
$form = $factory->create(TextType::class);
try {
    echo $form->isValid() ? 'valid' : 'invalid';
} catch (Throwable $e) {
    echo get_class($e);
}"""

T23_L9 = """use Symfony\\Component\\DependencyInjection\\ContainerBuilder;
$c = new ContainerBuilder();
$c->register('a', stdClass::class)->setPublic(true);
$c->compile();
echo spl_object_id($c->get('a')) === spl_object_id($c->get('a')) ? 'same' : 'diff';"""

T23_L10 = """use Symfony\\Component\\DependencyInjection\\ContainerBuilder;
$c = new ContainerBuilder();
$c->setParameter('name', 'Bob');
$c->setParameter('greeting', 'hi %name%');
$c->compile();
echo $c->getParameter('greeting');"""

T25_L9 = """namespace App\\Domain;
use App\\Application\\PlaceOrder;
use App\\Infrastructure\\Db;

class OrderService
{
    public function __construct(
        private PlaceOrder $handler,
        private Db $db,
    ) {}
}"""

T25_L10 = """namespace App\\Domain;
use App\\Application\\PlaceOrder;
use App\\Infrastructure\\Db;

class BillingService
{
    public function __construct(
        private PlaceOrder $handler,
        private Db $db,
        private \\App\\Infrastructure\\Logger $logger,
    ) {}
}"""

T26_L9 = """final class Money
{
    public function __construct(public readonly int $amount) {}
    public function add(Money $o): Money
    {
        return new self($this->amount + $o->amount);
    }
}
$a = new Money(10);
$c = $a->add(new Money(5));
echo $a->amount . ',' . $c->amount;"""

T26_L10 = """final class Order
{
    private array $items = [];

    public function add(string $sku): void
    {
        $this->items[$sku] = true;
    }

    public function remove(string $sku): void
    {
        if (count($this->items) === 1) {
            throw new DomainException('last item');
        }
        unset($this->items[$sku]);
    }
}
$o = new Order;
$o->add('a');
try {
    $o->remove('a');
} catch (Throwable $e) {
    echo get_class($e);
}"""

T15_L9 = """CREATE TEMP TABLE t (x int);
INSERT INTO t VALUES (1), (NULL);
SELECT count(x) FROM t;"""

T15_L10 = """CREATE TEMP TABLE t (id int, x int);
INSERT INTO t VALUES (1, 10), (2, 20), (3, 30);
SELECT string_agg(s::text, ',' ORDER BY id) FROM (
    SELECT id, sum(x) OVER (ORDER BY id) AS s FROM t
) q;"""

T17_L9 = """CREATE TEMP TABLE t (a int);
CREATE UNIQUE INDEX ON t (a);
INSERT INTO t VALUES (1) ON CONFLICT DO NOTHING;
INSERT INTO t VALUES (1) ON CONFLICT DO NOTHING;
SELECT count(*) FROM t;"""

T17_L10 = """BEGIN;
CREATE TEMP TABLE t (a int, UNIQUE (a) DEFERRABLE INITIALLY DEFERRED);
INSERT INTO t VALUES (1);
INSERT INTO t VALUES (1);
SELECT count(*) FROM t;"""

T19_L9 = """curl -s -d 'a=1' http://demo.local/api"""

T19_L10 = """curl -s -G -d 'a=1' http://demo.local/api"""

T14_L9 = """$u = new User(name: 'A');
$em->persist($u);
$em->flush();
$a = $em->find(User::class, $u->getId());
$b = $em->find(User::class, $u->getId());
echo spl_object_id($a) === spl_object_id($b) ? 'same' : 'diff';"""

T14_L10 = """$u = new User(name: 'B');
$em->persist($u);
$em->remove($u);
$em->flush();
echo count($em->getConnection()->fetchAllAssociative('SELECT * FROM users'));"""

DEP_RULES = {
    "ru": ("Слои: Domain, Application, Infrastructure. Правила deptrac: "
           "Application → Domain; Infrastructure → Domain, Application; "
           "Domain → только Domain."),
    "en": ("Layers: Domain, Application, Infrastructure. Deptrac rules: "
           "Application → Domain; Infrastructure → Domain, Application; "
           "Domain → Domain only."),
    "zh": ("层：Domain、Application、Infrastructure。deptrac 规则："
           "Application → Domain；Infrastructure → Domain、Application；"
           "Domain → 仅 Domain。"),
}


def php_prompt(code: str, lang: str) -> str:
    frame = {"ru": "Что выведет этот код?",
             "en": "What does this code output?",
             "zh": "这段代码会输出什么？"}[lang]
    return f"{frame}\n\n```php\n{code}\n```"


CELLS = [
    dict(id="T01-L9", level=9, code=T01_L9, exp="3-3",
         grader=None,  # php-эталон: код из prompt считается интерпретатором
         note={"ru": "intdiv обрезает к нулю: intdiv(-7, 2) = -3, не -4.",
               "en": "intdiv truncates toward zero: intdiv(-7, 2) = -3, not -4.",
               "zh": "intdiv 向零截断：intdiv(-7, 2) = -3，不是 -4。"}),
    dict(id="T01-L10", level=10, code=T01_L10, exp="-9",
         grader={"regex": "^-9$", "accept": ["-9"]},
         note={"ru": "** правоассоциативен и связывает крепче унарного минуса: -(3²).",
               "en": "** is right-associative and binds tighter than unary minus: -(3²).",
               "zh": "** 右结合且优先级高于一元负号：-(3²)。"}),
    dict(id="T02-L9", level=9, code=T02_L9, exp="true",
         note={"ru": "Обе строки числовые — сравнение как чисел: 10 == 10.",
               "en": "Both strings are numeric — compared as numbers: 10 == 10.",
               "zh": "两个字符串都是数值型——按数字比较：10 == 10。"}),
    dict(id="T02-L10", level=10, code=T02_L10, exp="other",
         note={"ru": "PHP 8: 'a' == 0 → false (раньше было true) — срабатывает default.",
               "en": "PHP 8: 'a' == 0 → false (used to be true) — default fires.",
               "zh": "PHP 8：'a' == 0 → false（以前是 true）——触发 default。"}),
    dict(id="T03-L9", level=9, code=T03_L9, exp="2c",
         note={"ru": "strtr с массивом предпочитает самое длинное совпадение ключа.",
               "en": "strtr with an array prefers the longest matching key.",
               "zh": "带数组的 strtr 优先匹配最长的键。"}),
    dict(id="T03-L10", level=10, code=T03_L10, exp="[hello]",
         note={"ru": "Отрицательный старт за пределами строки зажимается к началу.",
               "en": "A negative start beyond the string is clamped to the beginning.",
               "zh": "超出字符串范围的负起始位置会被钳制到开头。"}),
    dict(id="T04-L9", level=9, code=T04_L9, exp="k,n,m",
         note={"ru": "Оператор + : при дубликатах строковых ключей побеждает левый массив.",
               "en": "The + operator: on duplicate string keys the left array wins.",
               "zh": "运算符 +：字符串键重复时左数组优先。"}),
    dict(id="T04-L10", level=10, code=T04_L10, exp="yxz",
         note={"ru": "Сортировка стабильна с 8.0: x и z при равном ключе сохраняют порядок.",
               "en": "Sorts are stable since 8.0: x and z keep their order on a tie.",
               "zh": "自 8.0 起排序稳定：键相同时 x 和 z 保持原顺序。"}),
    dict(id="T05-L9", level=9, code=T05_L9, exp="4",
         note={"ru": "Строковые ключи распакованного массива становятся именованными аргументами.",
               "en": "String keys of a spread array become named arguments.",
               "zh": "展开数组的字符串键会成为命名参数。"}),
    dict(id="T05-L10", level=10, code=T05_L10, exp="3",
         note={"ru": "Первая-class callable синтаксис привязывает $this при создании.",
               "en": "First-class callable syntax binds $this at creation time.",
               "zh": "一等可调用语法在创建时就绑定了 $this。"}),
    dict(id="T06-L9", level=9, code=T06_L9, exp="B",
         note={"ru": "Позднее статическое связывание: static::class = класс получателя.",
               "en": "Late static binding: static::class is the receiver's class.",
               "zh": "后期静态绑定：static::class 是接收者的类。"}),
    dict(id="T06-L10", level=10, code=T06_L10, exp="10",
         note={"ru": "Внутри set-хука $this->x пишет в backing store (без рекурсии): 5*2.",
               "en": "Inside the set hook, $this->x writes the backing store (no recursion): 5*2.",
               "zh": "在 set 钩子内 $this->x 写入后备存储（无递归）：5*2。"}),
    dict(id="T07-L9", level=9, code=T07_L9, exp="BA",
         note={"ru": "insteadof выбирает реализацию, as оставляет вторую под другим именем.",
               "en": "insteadof picks the implementation; as keeps the other under an alias.",
               "zh": "insteadof 选择实现，as 以别名保留另一个。"}),
    dict(id="T07-L10", level=10, code=T07_L10, exp="0",
         note={"ru": "Статическое свойство трейта копируется в каждый использующий класс.",
               "en": "A trait's static property is copied into each consuming class.",
               "zh": "trait 的静态属性会复制到每个使用它的类中。"}),
    dict(id="T08-L9", level=9, code=T08_L9, exp="2",
         note={"ru": "return в finally перезаписывает return из try.",
               "en": "A return in finally overrides the return from try.",
               "zh": "finally 中的 return 会覆盖 try 中的 return。"}),
    dict(id="T08-L10", level=10, code=T08_L10, exp="second",
         note={"ru": "Исключение из finally подменяет уже летящее — ловится последнее.",
               "en": "An exception from finally replaces the in-flight one — the last thrown is caught.",
               "zh": "finally 抛出的异常会替换正在传播的异常——捕获最后抛出的那个。"}),
    dict(id="T09-L9", level=9, code=T09_L9, exp="App\\Foo|Baz",
         note={"ru": "use-алиас действует на неквалифицированные имена; ведущий \\ уводит в глобальную область.",
               "en": "The use alias applies to unqualified names; a leading \\ escapes to the global scope.",
               "zh": "use 别名只作用于非限定名；前导 \\ 转向全局作用域。"}),
    dict(id="T09-L10", level=10, code=T09_L10, exp="noyes",
         note={"ru": "Динамические имена не резолвятся относительно namespace — 'Foo' это глобальный класс.",
               "en": "Dynamic names are not resolved against the namespace — 'Foo' is the global class.",
               "zh": "动态名称不会相对于命名空间解析——'Foo' 指全局类。"}),
    dict(id="T10-L9", level=9, code=T10_L9, exp="1,2,99",
         note={"ru": "После foreach по ссылке $v остаётся ссылкой на последний элемент.",
               "en": "After a by-ref foreach, $v remains a reference to the last element.",
               "zh": "按引用的 foreach 结束后，$v 仍是对最后一个元素的引用。"}),
    dict(id="T10-L10", level=10, code=T10_L10, exp="3",
         note={"ru": "static-переменная метода одна на метод родителя — наследники делят её.",
               "en": "A method's static variable belongs to the parent's method — subclasses share it.",
               "zh": "方法的静态变量属于父类方法——子类共享同一个。"}),
    dict(id="T11-L9", level=9, code=T11_L9, exp="11",
         note={"ru": "send() возобновляет генератор: $x = 10, return 10+1.",
               "en": "send() resumes the generator: $x = 10, return 10+1.",
               "zh": "send() 恢复生成器：$x = 10，return 10+1。"}),
    dict(id="T11-L10", level=10, code=T11_L10, exp="ok,2",
         note={"ru": "yield from возвращает return-значение делегированного генератора.",
               "en": "yield from yields the delegated generator's return value last.",
               "zh": "yield from 最后会产出被委托生成器的返回值。"}),
    dict(id="T12-L9", level=9, code=T12_L9, exp="YTpi",
         note={"ru": "base64 'a:b' — три байта 61:3a:62 → YTpi.",
               "en": "base64 of 'a:b' — three bytes 61:3a:62 → YTpi.",
               "zh": "'a:b' 的 base64——三个字节 61:3a:62 → YTpi。"}),
    dict(id="T12-L10", level=10, code=T12_L10, exp="30711",
         note={"ru": "E_ALL (30719) минус E_NOTICE (8) = 30711 — error_reporting возвращает установленный уровень.",
               "en": "E_ALL (30719) minus E_NOTICE (8) = 30711 — error_reporting returns the set level.",
               "zh": "E_ALL（30719）减 E_NOTICE（8）= 30711——error_reporting 返回设置的级别。"}),
    dict(id="T13-L9", level=9, code=T13_L9, exp="abc",
         grader={"accept": ["abc"]},
         note={"ru": "HttpFoundation маппит HTTP_X_TOKEN серверных параметров в заголовок X-Token (регистр не важен).",
               "en": "HttpFoundation maps the HTTP_X_TOKEN server param to the X-Token header (case-insensitive).",
               "zh": "HttpFoundation 把服务器参数 HTTP_X_TOKEN 映射为 X-Token 头（大小写不敏感）。"}),
    dict(id="T13-L10", level=10, code=T13_L10, exp="BCA",
         grader={"accept": ["BCA"]},
         note={"ru": "Порядок листенеров — по убыванию приоритета: 10 (B), 5 (C), 0 (A).",
               "en": "Listeners run by descending priority: 10 (B), 5 (C), 0 (A).",
               "zh": "监听器按优先级降序执行：10 (B)、5 (C)、0 (A)。"}),
    dict(id="T14-L9", level=9, ctx="doctrine", code=T14_L9, exp="same",
         grader={"accept": ["same"]},
         note={"ru": "Identity map: повторный find того же id возвращает тот же инстанс.",
               "en": "Identity map: a second find for the same id returns the same instance.",
               "zh": "identity map：对同一 id 的再次 find 返回同一个实例。"}),
    dict(id="T14-L10", level=10, ctx="doctrine", code=T14_L10, exp="0",
         grader={"accept": ["0"]},
         note={"ru": "Unit of work откладывает INSERT до flush: remove после persist отменяет вставку.",
               "en": "The unit of work schedules INSERT at flush: remove after persist cancels it.",
               "zh": "工作单元在 flush 时才安排 INSERT：persist 后 remove 会取消插入。"}),
    dict(id="T15-L9", level=9, sql=T15_L9, exp="1",
         grader={"accept": ["1"]},
         note={"ru": "count(столбец) не считает NULL; count(*) считал бы 2.",
               "en": "count(column) skips NULLs; count(*) would return 2.",
               "zh": "count(列) 不计 NULL；count(*) 会返回 2。"}),
    dict(id="T15-L10", level=10, sql=T15_L10, exp="10,30,60",
         grader={"accept": ["10,30,60", "10 30 60"]},
         note={"ru": "С ORDER BY кадр окна по умолчанию — running (от начала до текущей строки).",
               "en": "With ORDER BY the default window frame is running (start to current row).",
               "zh": "有 ORDER BY 时窗口默认帧是 running（从起点到当前行）。"}),
    dict(id="T16-L9", level=9, code=T16_L9, exp="M1M2",
         grader={"accept": ["M1M2"]},
         note={"ru": "Первым добавленный middleware выполняется первым (внешний).",
               "en": "The first-added middleware runs first (outermost).",
               "zh": "先添加的中间件先执行（最外层）。"}),
    dict(id="T16-L10", level=10, code=T16_L10, exp="11",
         grader={"accept": ["11"]},
         note={"ru": "Все зарегистрированные хендлеры сообщения вызываются: 1 + 10 = 11.",
               "en": "All registered handlers of the message run: 1 + 10 = 11.",
               "zh": "消息的所有已注册处理器都会执行：1 + 10 = 11。"}),
    dict(id="T17-L9", level=9, sql=T17_L9, exp="1",
         grader={"accept": ["1"]},
         note={"ru": "ON CONFLICT DO NOTHING пропускает дубликат: вставка одна.",
               "en": "ON CONFLICT DO NOTHING skips the duplicate: one insert.",
               "zh": "ON CONFLICT DO NOTHING 跳过重复：只插入一行。"}),
    dict(id="T17-L10", level=10, sql=T17_L10, exp="2",
         grader={"accept": ["2"]},
         note={"ru": "DEFERRED откладывает проверку уникальности до COMMIT — count видит 2 строки.",
               "en": "DEFERRED postpones the uniqueness check to COMMIT — count sees 2 rows.",
               "zh": "DEFERRED 把唯一性检查推迟到 COMMIT——count 看到 2 行。"}),
    dict(id="T18-L9", level=9, code=T18_L9, exp="6",
         grader={"accept": ["6"]},
         cmd={"ru": "Команда: `phpunit CalcTest.php --filter test_sum`",
              "en": "Command: `phpunit CalcTest.php --filter test_sum`",
              "zh": "命令：`phpunit CalcTest.php --filter test_sum`"},
         note={"ru": "--filter — подстрочное совпадение: test_sum ловит и test_sum_abs (3+3 датасета).",
               "en": "--filter is a substring match: test_sum also matches test_sum_abs (3+3 datasets).",
               "zh": "--filter 是子串匹配：test_sum 也会匹配 test_sum_abs（3+3 个数据集）。"}),
    dict(id="T18-L10", level=10, code=T18_L10, exp="2",
         grader={"accept": ["2"]},
         cmd={"ru": "Команда: `phpunit Calc2Test.php`",
              "en": "Command: `phpunit Calc2Test.php`",
              "zh": "命令：`phpunit Calc2Test.php`"},
         note={"ru": "Не-static провайдер — только deprecation в PHPUnit 10: оба датасета выполнятся.",
               "en": "A non-static provider is just a deprecation in PHPUnit 10: both datasets run.",
               "zh": "在 PHPUnit 10 中非静态 provider 只是 deprecation：两个数据集都会执行。"}),
    dict(id="T19-L9", level=9, cmd=T19_L9, exp="POST",
         grader={"accept": ["POST"]},
         note={"ru": "-d/-F переключает curl на POST по умолчанию.",
               "en": "-d/-F switches curl to POST by default.",
               "zh": "-d/-F 会让 curl 默认使用 POST。"}),
    dict(id="T19-L10", level=10, cmd=T19_L10, exp="GET",
         grader={"accept": ["GET"]},
         note={"ru": "-G превращает данные из -d в query string, метод остаётся GET.",
               "en": "-G turns -d data into the query string; the method stays GET.",
               "zh": "-G 把 -d 的数据变成查询串，方法仍是 GET。"}),
    dict(id="T20-L9", level=9, exp="invalid_client",
         grader={"accept": ["invalid_client"]},
         prompt={"ru": "Клиент обращается к токен-эндпоинту (RFC 6749) с несуществующим client_id. "
                       "Какое значение поля error вернёт эндпоинт?",
                 "en": "A client hits the token endpoint (RFC 6749) with a non-existent client_id. "
                       "What value of the error field does the endpoint return?",
                 "zh": "客户端用不存在的 client_id 请求令牌端点（RFC 6749）。端点的 error 字段返回什么值？"},
         note={"ru": "Неизвестный клиент → invalid_client (RFC 6749 §5.2).",
               "en": "Unknown client → invalid_client (RFC 6749 §5.2).",
               "zh": "未知客户端 → invalid_client（RFC 6749 §5.2）。"}),
    dict(id="T20-L10", level=10, exp="fragment",
         grader={"accept": ["fragment", "фрагмент", "片段"]},
         prompt={"ru": "OAuth 2.0: клиент начал поток с response_type=token. "
                       "В какой части redirect_uri авторизационный сервер передаёт access_token?",
                 "en": "OAuth 2.0: the client started the flow with response_type=token. "
                       "In which part of the redirect_uri does the authorization server pass the access_token?",
                 "zh": "OAuth 2.0：客户端以 response_type=token 开始流程。授权服务器把 access_token 放在 redirect_uri 的哪个部分？"},
         note={"ru": "Implicit-поток передаёт токен в фрагменте URL (#) — он не уходит на сервер.",
               "en": "The implicit flow passes the token in the URL fragment (#) — it never reaches the server.",
               "zh": "隐式流通过 URL 片段（#）传递令牌——它不会到达服务器。"}),
    dict(id="T21-L9", level=9, code=T21_L9, exp="c",
         grader={"accept": ["c"]},
         lang="twig",
         note={"ru": "default срабатывает не только на undefined, но и на falsy.",
               "en": "default triggers not only on undefined but on any falsy value.",
               "zh": "default 不仅对 undefined 生效，对任何 falsy 值都生效。"}),
    dict(id="T21-L10", level=10, code=T21_L10, exp="RuntimeError",
         grader={"accept": ["RuntimeError", "Twig\\Error\\RuntimeError"]},
         lang="twig",
         prompt={"ru": "Какой класс исключения выбросит render() этого шаблона?\n\n```twig\n{code}\n```",
                 "en": "What exception class does render() of this template throw?\n\n```twig\n{code}\n```",
                 "zh": "渲染这个模板时 render() 会抛出哪个异常类？\n\n```twig\n{code}\n```"},
         note={"ru": "В Twig ~ связывает крепче +: ('a' ~ 1) + 2 → string + int → TypeError/RuntimeError.",
               "en": "In Twig ~ binds tighter than +: ('a' ~ 1) + 2 → string + int → TypeError/RuntimeError.",
               "zh": "在 Twig 中 ~ 的优先级高于 +：('a' ~ 1) + 2 → string + int → TypeError/RuntimeError。"}),
    dict(id="T22-L9", level=9, code=T22_L9, exp="0",
         grader={"accept": ["0"]},
         note={"ru": "NotBlank считает пустым null, '', [] — массив [''] не пуст.",
               "en": "NotBlank treats null, '', [] as blank — the array [''] is not blank.",
               "zh": "NotBlank 把 null、''、[] 视为空——数组 [''] 不算空。"}),
    dict(id="T22-L10", level=10, code=T22_L10,
         exp="Symfony\\Component\\Form\\Exception\\LogicException",
         grader={"accept": ["Symfony\\Component\\Form\\Exception\\LogicException",
                            "LogicException"]},
         note={"ru": "isValid() на неотправленной форме — LogicException, не false.",
               "en": "isValid() on a non-submitted form throws LogicException, it does not return false.",
               "zh": "对未提交的表单调用 isValid() 抛出 LogicException，而不是返回 false。"}),
    dict(id="T23-L9", level=9, code=T23_L9, exp="same",
         grader={"accept": ["same"]},
         note={"ru": "Сервисы в контейнере общие (shared): get() дважды — один инстанс.",
               "en": "Container services are shared: two get() calls yield one instance.",
               "zh": "容器中的服务是共享的：两次 get() 得到同一实例。"}),
    dict(id="T23-L10", level=10, code=T23_L10, exp="hi Bob",
         grader={"accept": ["hi Bob"]},
         note={"ru": "compile() резолвит %-ссылки между параметрами: 'hi %name%' → 'hi Bob'.",
               "en": "compile() resolves %-references between parameters: 'hi %name%' → 'hi Bob'.",
               "zh": "compile() 会解析参数间的 % 引用：'hi %name%' → 'hi Bob'。"}),
    dict(id="T24-L9", level=9, exp="960",
         grader={"accept": ["960", "960px"]},
         prompt={"ru": "Какая max-width у .container в Bootstrap 5.3 при ширине окна 992px?",
                 "en": "What is the max-width of .container in Bootstrap 5.3 at a 992px viewport?",
                 "zh": "在 Bootstrap 5.3 中，视口宽度 992px 时 .container 的 max-width 是多少？"},
         note={"ru": "Container-ширины 5.x: 540/720/960/1140/1320 (sm–xxl); 992 → 960px.",
               "en": "5.x container widths: 540/720/960/1140/1320 (sm–xxl); 992 → 960px.",
               "zh": "5.x 的 container 宽度：540/720/960/1140/1320（sm–xxl）；992 → 960px。"}),
    dict(id="T24-L10", level=10, exp="text-danger",
         grader={"accept": ["text-danger"]},
         prompt={"ru": "Какая утилита определит цвет текста в Bootstrap 5.3: "
                       "`<p class=\"text-danger text-primary\">…</p>`? Ответь именем класса-победителя.",
                 "en": "Which utility sets the text color in Bootstrap 5.3 for "
                       "`<p class=\"text-danger text-primary\">…</p>`? Answer with the winning class name.",
                 "zh": "在 Bootstrap 5.3 中，`<p class=\"text-danger text-primary\">…</p>` 的文字颜色由哪个工具类决定？回答胜出的类名。"},
         note={"ru": "Специфичность равна — победит правило позже в css: text-danger объявлен после text-primary.",
               "en": "Specificity ties — the later rule wins: text-danger is declared after text-primary.",
               "zh": "特异性相同——后声明的规则获胜：text-danger 在 text-primary 之后声明。"}),
    dict(id="T25-L9", level=9, code=T25_L9, exp="2",
         grader={"accept": ["2"]},
         prompt={"ru": DEP_RULES["ru"] + "\n\nСколько нарушений (Violations) покажет deptrac для этого класса?\n\n```php\n{code}\n```",
                 "en": DEP_RULES["en"] + "\n\nHow many violations will deptrac report for this class?\n\n```php\n{code}\n```",
                 "zh": DEP_RULES["zh"] + "\n\n对于这个类，deptrac 会报告多少个违规（Violations）？\n\n```php\n{code}\n```"},
         note={"ru": "Domain может зависеть только от Domain: PlaceOrder (Application) + Db (Infrastructure) = 2.",
               "en": "Domain may depend only on Domain: PlaceOrder (Application) + Db (Infrastructure) = 2.",
               "zh": "Domain 只能依赖 Domain：PlaceOrder（Application）+ Db（Infrastructure）= 2。"}),
    dict(id="T25-L10", level=10, code=T25_L10, exp="3",
         grader={"accept": ["3"]},
         prompt={"ru": DEP_RULES["ru"] + "\n\nСколько нарушений (Violations) покажет deptrac для этого класса?\n\n```php\n{code}\n```",
                 "en": DEP_RULES["en"] + "\n\nHow many violations will deptrac report for this class?\n\n```php\n{code}\n```",
                 "zh": DEP_RULES["zh"] + "\n\n对于这个类，deptrac 会报告多少个违规（Violations）？\n\n```php\n{code}\n```"},
         note={"ru": "Inline-FQCN \\App\\Infrastructure\\Logger тоже считается: Application + Db + Logger = 3.",
               "en": "The inline FQCN \\App\\Infrastructure\\Logger counts too: Application + Db + Logger = 3.",
               "zh": "内联 FQCN \\App\\Infrastructure\\Logger 也计入：Application + Db + Logger = 3。"}),
    dict(id="T26-L9", level=9, code=T26_L9, exp="10,15",
         note={"ru": "VO иммутабелен: add() возвращает новый инстанс, исходный не меняется.",
               "en": "The VO is immutable: add() returns a new instance; the original is unchanged.",
               "zh": "值对象不可变：add() 返回新实例，原对象不变。"}),
    dict(id="T26-L10", level=10, code=T26_L10, exp="DomainException",
         note={"ru": "Инвариант агрегата: удаление последнего товара запрещено — DomainException.",
               "en": "Aggregate invariant: removing the last item is forbidden — DomainException.",
               "zh": "聚合不变量：不允许移除最后一个商品——DomainException。"}),
]
