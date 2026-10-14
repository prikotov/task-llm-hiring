"""Локальные env-значения из `.env.local` в корне workspace (в git не живёт).

Даёт скриптам адреса серверов и пути к ключам без хардкода: KEY=VALUE по
строке, `#` — комментарий, ведущий `~` в значении разворачивается в домашний
каталог. Уже экспортированные переменные окружения главнее (setdefault).
"""

import os
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent


def load_env_local() -> None:
    env_file = ROOT / ".env.local"
    if not env_file.exists():
        return
    for line in env_file.read_text().splitlines():
        line = line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        name, _, value = line.partition("=")
        os.environ.setdefault(name.strip(), os.path.expanduser(value.strip()))


load_env_local()
