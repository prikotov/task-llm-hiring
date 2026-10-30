<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Общая обвязка: служебные сообщения — в stderr.
 *
 * Совместимо и с ConsoleOutput (CLI), и с BufferedOutput (тесты),
 * где getErrorOutput может отсутствовать.
 */
abstract class AppCommand extends Command
{
    protected function errorStream(OutputInterface $output): OutputInterface
    {
        return method_exists($output, 'getErrorOutput') ? $output->getErrorOutput() : $output;
    }

    protected function error(OutputInterface $output, string $message): void
    {
        $this->errorStream($output)->writeln('<error>' . $message . '</error>');
    }

    protected function note(OutputInterface $output, string $message): void
    {
        $this->errorStream($output)->writeln($message);
    }
}
