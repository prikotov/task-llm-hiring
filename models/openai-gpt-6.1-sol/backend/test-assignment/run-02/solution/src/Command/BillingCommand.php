<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

abstract class BillingCommand extends Command
{
    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->perform($input, $output);

            return Command::SUCCESS;
        } catch (\Throwable $exception) {
            $this->stderr($output)->writeln('Ошибка: ' . $exception->getMessage(), OutputInterface::OUTPUT_RAW);

            return Command::FAILURE;
        }
    }

    protected function stderr(OutputInterface $output): OutputInterface
    {
        return $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : new StreamOutput(STDERR);
    }

    abstract protected function perform(InputInterface $input, OutputInterface $output): void;
}
