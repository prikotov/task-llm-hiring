#!/usr/bin/env python3
"""Батарея машинной верификации эталонов зоны «Глубина» (deep_cells.py).

Каждый эталон перепроверяется на машине теста до заморозки вопросника:
    php-клетки   — интерпретатор 8.5 считает вывод кода из prompt (дважды,
                   детерминизм), сравнение с exp;
    vendor-клетки — код с autoload TasK (Symfony: HttpFoundation,
                   EventDispatcher, Messenger, Validator, Form, DI);
    Doctrine     — ORM 3 на SQLite in-memory (фикстура с entity);
    PHPUnit      — прогон vendor/bin/phpunit на фикстурах;
    PostgreSQL   — docker psql (контейнер KM_PG_CONTAINER, по умолчанию
                   km-verify-pg: postgres:17-alpine);
    curl         — заглушка php -S + реальный curl;
    deptrac      — фикстуры слоёв + vendor/bin/deptrac;
    Bootstrap    — pinned bootstrap 5.3.3 (CDN, кэш в /tmp/km-verify);
    OAuth        — документ (RFC 6749 §5.2, §4.2.2) — исполнению не подлежит.

Переменные окружения: TASK_VENDOR — путь к vendor TasK (обязательна для
vendor/doctrine/phpunit/deptrac-клеток), KM_PG_CONTAINER — контейнер psql.
Запуск: python3 knowledge-map/verify_deep.py
"""
import json
import os
import re
import shlex
import shutil
import subprocess
import sys
import tempfile
import time
import urllib.request
from pathlib import Path

SCRIPT_DIR = Path(__file__).parent
sys.path.insert(0, str(SCRIPT_DIR))
sys.path.insert(0, str(SCRIPT_DIR.parent / "interview"))
import env_local  # noqa: F401,E402 — грузит .env.local (TASK_VENDOR и др.)
from deep_cells import CELLS  # noqa: E402

VENDOR = os.environ.get("TASK_VENDOR")
PG = os.environ.get("KM_PG_CONTAINER", "km-verify-pg")
BOOTSTRAP_URL = ("https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/"
                 "dist/css/bootstrap.css")
CACHE = Path(tempfile.gettempdir()) / "km-verify"
DOC_CELLS = {"T20-L9": "RFC 6749 §5.2: invalid_client",
             "T20-L10": "RFC 6749 §4.2.2: токен в фрагменте URL"}

RESULTS: list[tuple[str, str, str]] = []


def run(cmd: list, timeout: int = 60,
        cwd: Path | None = None) -> subprocess.CompletedProcess:
    return subprocess.run(cmd, capture_output=True, text=True,
                          timeout=timeout, cwd=cwd)


def php_code(code: str, vendor: bool = False) -> str:
    head = f"<?php\nrequire '{VENDOR}/autoload.php';\n" if vendor else "<?php\n"
    f = CACHE / "probe.php"
    f.write_text(head + code + "\n", encoding="utf-8")
    r = run(["php", str(f)])
    if r.returncode != 0:
        raise RuntimeError(f"php fail: {r.stderr.strip()[:200]}")
    return r.stdout.strip()


def check_php(c: dict) -> str:
    """php-эталон: код из клетки считает интерпретатор, дважды — детерминизм."""
    out1 = php_code(c["code"])
    out2 = php_code(c["code"])
    if out1 != out2:
        return f"недетерминизм: {out1!r} != {out2!r}"
    if out1 != c["exp"]:
        return f"вывод {out1!r} != exp {c['exp']!r}"
    return "ok"


def match_ethalon(out: str, exp: str) -> bool:
    """Эталон вывода: точное совпадение или вхождение на границах «слов»
    (для namespace-имён классов: короткое имя в полном имени зачтено быть
    не может, полное содержит короткое — как accept-грейдер раннера)."""
    if out == exp:
        return True
    return re.search(rf"(?<![\w\\_]){re.escape(exp)}(?![\w\\-])", out) is not None


def check_vendor(c: dict) -> str:
    out = php_code(c["code"], vendor=True)
    if not match_ethalon(out, c["exp"]):
        return f"вывод {out!r} != exp {c['exp']!r}"
    return "ok"


DOCTRINE_ENTITY = """<?php
declare(strict_types=1);
namespace Km\\Fix;
use Doctrine\\ORM\\Mapping as ORM;
#[ORM\\Entity]
#[ORM\\Table(name: 'users')]
class User
{
    public function __construct(
        #[ORM\\Id, ORM\\Column(type: 'integer')]
        #[ORM\\GeneratedValue]
        private(set) int $id = 0,
        #[ORM\\Column(type: 'string')]
        public string $name = '',
    ) {}
    public function getId(): int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }
}
"""


def check_doctrine(c: dict) -> str:
    """ORM 3 на SQLite in-memory: свежая схема на каждую клетку."""
    d = CACHE / "doct"
    d.mkdir(parents=True, exist_ok=True)
    (d / "km_entity.php").write_text(DOCTRINE_ENTITY, encoding="utf-8")
    script = f"""<?php
require '{VENDOR}/autoload.php';
require '{d / "km_entity.php"}';
use Doctrine\\DBAL\\DriverManager;
use Doctrine\\ORM\\EntityManager;
use Doctrine\\ORM\\ORMSetup;
use Km\\Fix\\User;
$config = ORMSetup::createAttributeMetadataConfiguration(['{d}'], true);
$config->enableNativeLazyObjects(true);
$conn = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true], $config);
$em = new EntityManager($conn, $config);
$tool = new \\Doctrine\\ORM\\Tools\\SchemaTool($em);
$md = $em->getClassMetadata(\\Km\\Fix\\User::class);
$tool->createSchema([$md]);
{c["code"]}
"""
    (d / "run.php").write_text(script, encoding="utf-8")
    r = run(["php", str(d / "run.php")], timeout=120)
    if r.returncode != 0:
        return f"php fail: {r.stderr.strip()[-200:]}"
    out = r.stdout.strip()
    if out != c["exp"]:
        return f"вывод {out!r} != exp {c['exp']!r}"
    return "ok"


def check_phpunit(c: dict) -> str:
    """Прогон phpunit на фикстуре; парсим число выполненных тестов."""
    d = CACHE / "phpunit"
    d.mkdir(parents=True, exist_ok=True)
    cls = "CalcTest" if c["id"] == "T18-L9" else "Calc2Test"
    (d / f"{cls}.php").write_text("<?php\n" + c["code"], encoding="utf-8")
    args = ["--filter", "test_sum"] if c["id"] == "T18-L9" else []
    r = run([f"{VENDOR}/bin/phpunit", f"{cls}.php", *args], timeout=120,
            cwd=d)
    m = (re.search(r"Tests: (\d+)", r.stdout)
         or re.search(r"\((\d+) tests", r.stdout))
    if not m:
        return f"нет счётчика тестов в выводе: {r.stdout[:150]!r}"
    if int(m.group(1)) != int(c["exp"]):
        return f"тестов {m.group(1)} != exp {c['exp']}"
    return "ok"


def check_psql(c: dict) -> str:
    """psql печатает теги всех команд (CREATE TABLE, INSERT 0 N) — эталон
    в последней непустой строке stdout."""
    r = run(["docker", "exec", PG, "psql", "-U", "postgres", "-Atc", c["sql"]])
    lines = [x for x in r.stdout.strip().splitlines() if x.strip()]
    out = lines[-1] if lines else ""
    if out != c["exp"]:
        return (f"psql {out!r} (exit {r.returncode}) != exp {c['exp']!r}"
                f"{' | ' + r.stderr.strip()[:120] if r.returncode else ''}")
    return "ok"


TWIG_LOADER = """<?php
require '{vendor}/autoload.php';
use Twig\\Environment;
use Twig\\Loader\\ArrayLoader;
$template = <<<'TWIG'
{code}
TWIG;
try {{
    $t = new Environment(new ArrayLoader(['t' => $template]));
    echo $t->render('t');
}} catch (Throwable $e) {{
    $cls = get_class($e);
    echo str_contains($cls, 'Twig') ? (new ReflectionClass($cls))->getShortName() : $cls;
}}
"""


def check_twig(c: dict) -> str:
    script = TWIG_LOADER.format(vendor=VENDOR, code=c["code"])
    f = CACHE / "twig-probe.php"
    CACHE.mkdir(parents=True, exist_ok=True)
    f.write_text(script, encoding="utf-8")
    r = run(["php", str(f)])
    if r.returncode != 0:
        return f"php fail: {r.stderr.strip()[:200]}"
    out = r.stdout.strip()
    if out != c["exp"]:
        return f"вывод {out!r} != exp {c['exp']!r}"
    return "ok"


CURL_SERVER = """<?php
$method = $_SERVER['REQUEST_METHOD'] ?? '';
$ct = $_SERVER['CONTENT_TYPE'] ?? '';
file_put_contents(__DIR__ . '/curl-last.txt',
    "method=$method\\nct=$ct\\n");
header('Content-Type: text/plain');
echo "ok";
"""


def check_curl(c: dict) -> str:
    d = CACHE / "curl"
    d.mkdir(parents=True, exist_ok=True)
    (d / "server.php").write_text(CURL_SERVER, encoding="utf-8")
    last = d / "curl-last.txt"
    if last.exists():
        last.unlink()
    # старая заглушка могла пережить прошлый запуск — освобождаем порт
    run(["pkill", "-f", "php -S 127.0.0.1:18099"])
    time.sleep(0.3)
    srv = subprocess.Popen(
        ["php", "-S", "127.0.0.1:18099", str(d / "server.php")],
        stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    try:
        time.sleep(1.0)
        r = run(shlex.split(c["cmd"].replace(
            "http://demo.local/api", "http://127.0.0.1:18099/")))
        body = last.read_text(encoding="utf-8") if last.exists() else ""
        method = re.search(r"method=(\S+)", body)
        if not method:
            return f"заглушка не записала метод (curl exit {r.returncode})"
        if method.group(1) != c["exp"]:
            return f"метод {method.group(1)} != exp {c['exp']}"
        return "ok"
    finally:
        srv.terminate()
        srv.wait(timeout=10)


DEP_YAML = """deptrac:
  paths:
    - ./src
  layers:
    - name: Domain
      collectors:
        - type: class
          value: .*\\\\Domain\\\\.*
    - name: Application
      collectors:
        - type: class
          value: .*\\\\Application\\\\.*
    - name: Infrastructure
      collectors:
        - type: class
          value: .*\\\\Infrastructure\\\\.*
  ruleset:
    Domain:
    Application:
      - Domain
    Infrastructure:
      - Domain
      - Application
"""

DEP_FILES = {
    "Domain/OrderService.php": (
        "<?php\nnamespace App\\Domain;\nuse App\\Application\\PlaceOrder;\n"
        "use App\\Infrastructure\\Db;\nclass OrderService {\n"
        "    public function __construct(private PlaceOrder $h, private Db $db) {}\n}\n"),
    "Domain/BillingService.php": (
        "<?php\nnamespace App\\Domain;\nuse App\\Application\\PlaceOrder;\n"
        "use App\\Infrastructure\\Db;\nclass BillingService {\n"
        "    public function __construct(private PlaceOrder $h, private Db $db,\n"
        "        private \\App\\Infrastructure\\Logger $log) {}\n}\n"),
    "Application/PlaceOrder.php": (
        "<?php\nnamespace App\\Application;\nclass PlaceOrder {}\n"),
    "Infrastructure/Db.php": ("<?php\nnamespace App\\Infrastructure;\nclass Db {}\n"),
    "Infrastructure/Logger.php": ("<?php\nnamespace App\\Infrastructure;\nclass Logger {}\n"),
}


def check_deptrac(c: dict) -> str:
    d = CACHE / ("dep-l9" if c["id"] == "T25-L9" else "dep-l10")
    if d.exists():
        shutil.rmtree(d)
    (d / "src").mkdir(parents=True)
    (d / "deptrac.yaml").write_text(DEP_YAML, encoding="utf-8")
    for rel, content in DEP_FILES.items():
        # фиксatura клетки: L9 — без BillingService, L10 — без OrderService,
        # иначе нарушения второго класса смешаются в счёт
        if c["id"] == "T25-L9" and "Billing" in rel:
            continue
        if c["id"] == "T25-L10" and "OrderService" in rel:
            continue
        p = d / "src" / rel
        p.parent.mkdir(parents=True, exist_ok=True)
        p.write_text(content, encoding="utf-8")
    r = run([f"{VENDOR}/bin/deptrac", "analyze", "--no-progress"],
            timeout=120, cwd=d)
    m = re.search(r"Violations\s+(\d+)", r.stdout)
    if not m:
        return f"нет счётчика Violations: {r.stdout[:150]!r}"
    if int(m.group(1)) != int(c["exp"]):
        return f"нарушений {m.group(1)} != exp {c['exp']}"
    return "ok"


def check_bootstrap(c: dict) -> str:
    css_p = CACHE / "bootstrap-5.3.3.css"
    if not css_p.exists():
        CACHE.mkdir(parents=True, exist_ok=True)
        urllib.request.urlretrieve(BOOTSTRAP_URL, css_p)
    css = css_p.read_text(encoding="utf-8")
    if c["id"] == "T24-L9":
        if not re.search(r"min-width: 992px.*?max-width: 960px", css, re.S):
            return "в 5.3.3 при 992px у .container не 960px"
        return "ok"
    i1, i2 = css.find(".text-primary"), css.find(".text-danger")
    if i1 == -1 or i2 == -1:
        return "не нашёл утилиты text-primary/text-danger"
    if i2 <= i1:
        return "text-danger объявлен раньше text-primary — эталон неверен"
    return "ok"


CHECKS = {"T14-L9": check_doctrine, "T14-L10": check_doctrine,
          "T18-L9": check_phpunit, "T18-L10": check_phpunit,
          "T15-L9": check_psql, "T15-L10": check_psql,
          "T17-L9": check_psql, "T17-L10": check_psql,
          "T19-L9": check_curl, "T19-L10": check_curl,
          "T25-L9": check_deptrac, "T25-L10": check_deptrac,
          "T24-L9": check_bootstrap, "T24-L10": check_bootstrap,
          "T21-L9": check_twig, "T21-L10": check_twig}
VENDOR_IDS = {"T13-L9", "T13-L10", "T16-L9", "T16-L10",
              "T22-L9", "T22-L10", "T23-L9", "T23-L10"}


def main() -> int:
    if not VENDOR:
        raise SystemExit("задай TASK_VENDOR (путь к vendor TasK) в .env.local")
    CACHE.mkdir(parents=True, exist_ok=True)
    # вопросники собираем и сверяем: exp в клетках == эталон grader/accept
    subprocess.run([sys.executable, str(SCRIPT_DIR / "build_deep_cells.py")],
                   check=True)
    fails = 0
    for c in CELLS:
        cid = c["id"]
        if cid in DOC_CELLS:
            status = f"ok (документ: {DOC_CELLS[cid]})"
        elif cid in CHECKS:
            status = CHECKS[cid](c)
        elif cid in VENDOR_IDS:
            status = check_vendor(c)
        else:
            status = check_php(c)
        # грейдер-файлы и exp согласованы: accept[0] соответствует exp
        qdata = json.loads((SCRIPT_DIR / "questions-deep.json")
                           .read_text(encoding="utf-8"))
        q = next(q for q in qdata["questions"] if q["id"] == cid)
        acc = q["grader"].get("accept")
        if acc and status.startswith("ok") and c["exp"] not in (
                q["grader"].get("accept") or [c["exp"]]):
            # exp должен присутствовать в accept-списке (или быть php-выводом)
            norm = re.sub(r"\s+", "", c["exp"].lower())
            if not any(re.sub(r"\s+", "", a.lower()) == norm for a in acc):
                status = f"exp {c['exp']!r} нет в accept {acc}"
        RESULTS.append((cid, status, "OK" if status.startswith("ok") else "FAIL"))
        if not status.startswith("ok"):
            fails += 1
        print(f"  {cid}: {status}")
    n_ok = sum(1 for _, _, m in RESULTS if m == "OK")
    print(f"\nбатарея: {n_ok}/{len(CELLS)} зелёных")
    return 1 if fails else 0


if __name__ == "__main__":
    sys.exit(main())
