<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;

final class CommandSupport
{
    public static function fail(OutputInterface $output, string $message): int
    {
        self::errOut($output)->writeln('Ошибка: '.$message);

        return 1;
    }

    /**
     * Вывод диагностической информации в stderr.
     *
     * Не все реализации OutputInterface (например, StreamOutput из CommandTester)
     * умеют отдавать отдельный поток ошибок — в этом случае используем исходный.
     */
    public static function errOut(OutputInterface $output): OutputInterface
    {
        return method_exists($output, 'getErrorOutput') ? $output->getErrorOutput() : $output;
    }
}
