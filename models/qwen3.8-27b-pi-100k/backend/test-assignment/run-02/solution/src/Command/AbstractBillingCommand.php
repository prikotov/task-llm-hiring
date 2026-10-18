<?php

declare(strict_types=1);

namespace App\Command;

use App\Shared\Exception\BadInputException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Базовая консольная команда: все выводы — в stderr, stdout пуст,
 * код возврата 0 при успехе и 1 при ошибке.
 */
abstract class AbstractBillingCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            return $this->doExecute($input, $output->getErrorOutput());
        } catch (BadInputException $e) {
            $output->getErrorOutput()->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }
    }

    abstract protected function doExecute(InputInterface $input, OutputInterface $errorOut): int;

    protected function emit(OutputInterface $errorOut, string $message, bool $ok = true): void
    {
        $tag = $ok ? 'info' : 'error';
        $errorOut->writeln(sprintf("<{$tag}>%s</{$tag}>", $message));
    }
}
