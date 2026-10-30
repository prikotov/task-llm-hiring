<?php

declare(strict_types=1);

/**
 * Подготовка окружения тестов: автозагрузка, тестовая БД, миграции схемы.
 * Все шаги идемпотентны.
 */

$root = dirname(__DIR__);

require $root.'/vendor/autoload.php';

function billing_runConsole(array $params, string $root): void
{
    $cmd = array_merge(['php', $root.'/bin/console'], $params);
    $env = array_merge(getenv() ?: [], ['APP_ENV' => 'test']);

    $process = proc_open($cmd, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, $root, $env);
    if (!is_resource($process)) {
        throw new RuntimeException('Не удалось запустить bin/console');
    }

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $code = proc_close($process);

    if ($code !== 0) {
        throw new RuntimeException(sprintf("%s завершилась с кодом %d\n%s\n%s", implode(' ', $cmd), $code, $stdout, $stderr));
    }
}

billing_runConsole(['doctrine:database:create', '--if-not-exists'], $root);
billing_runConsole(['doctrine:migrations:migrate', '-n', '--no-interaction'], $root);
