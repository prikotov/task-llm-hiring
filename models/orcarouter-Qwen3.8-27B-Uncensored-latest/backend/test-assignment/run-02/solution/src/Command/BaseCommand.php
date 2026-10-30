<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Общим базовым класс для всех команд приложения. Унифицирует обработку
 * исключений и служебный вывод (в stderr), чтобы в stdout попадал только
 * "по-настоящему" полезный результат.
 */
abstract class BaseCommand extends Command
{
    protected function withErrorHandling(InputInterface $input, OutputInterface $output, callable $fn): int
    {
        try {
            return $fn($input, $output);
        } catch (\App\Exception\ImportException $e) {
            $output->getErrorOutput()->writeln('<error>' . $e->getMessage() . '</error>');
            return self::FAILURE;
        } catch (\Throwable $e) {
            $output->getErrorOutput()->writeln('<error>Ошибка: ' . $e->getMessage() . '</error>');
            return self::FAILURE;
        }
    }
}
