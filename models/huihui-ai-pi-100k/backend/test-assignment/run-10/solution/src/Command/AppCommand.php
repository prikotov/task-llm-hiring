<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Базовая команда: единый стиль служебного вывода и кода выхода.
 *
 * - stdout — публичный результат (отчёт, сводка);
 * - stderr — служебные сообщения (импорт, ошибки);
 * - code: 0 — успех, 1 — ошибка.
 */
abstract class AppCommand extends Command
{
    /**
     * Вывод на stderr.
     */
    protected function err(OutputInterface $output, string $line): void
    {
        $output->getErrorOutput()->writeln($line);
    }

    /**
     * Единая обработка ошибок: сообщение в stderr + код 1.
     */
    protected function fail(OutputInterface $output, \Throwable $e): int
    {
        $this->err($output, 'Error: ' . $e->getMessage());

        return self::FAILURE;
    }
}
