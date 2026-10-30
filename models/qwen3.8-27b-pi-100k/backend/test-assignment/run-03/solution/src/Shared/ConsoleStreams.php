<?php

declare(strict_types=1);

namespace App\Shared;

use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Доступ к служебным сообщениям: по спецификации они идут в stderr,
 * а stdout занят только результатами команд (отчёт CSV, сводки).
 */
final class ConsoleStreams
{
    public static function stderr(OutputInterface $output): OutputInterface
    {
        return $output instanceof ConsoleOutputInterface
            ? $output->getErrorOutput()
            : $output;
    }
}
