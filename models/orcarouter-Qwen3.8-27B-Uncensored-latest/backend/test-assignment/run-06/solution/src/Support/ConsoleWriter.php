<?php

declare(strict_types=1);

namespace App\Support;

use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Служебные сообщения команд пишутся в stderr, чтобы stdout оставался
 * чистым для машинно-читаемого вывода (CSV, сводки). bin/console всегда
 * передаёт ConsoleOutput, где разделение stdout/stderr доступно.
 */
final class ConsoleWriter
{
    private function __construct()
    {
    }

    public static function note(OutputInterface $output, string $message): void
    {
        $stream = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        $stream->writeln($message);
    }
}
