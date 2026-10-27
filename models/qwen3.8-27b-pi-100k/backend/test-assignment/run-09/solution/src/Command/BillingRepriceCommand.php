<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RepriceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Бонус: ретро-коррекция цены — применить исправленные строки цен и пересчитать затронутые события.',
)]
final class BillingRepriceCommand extends Command
{
    public function __construct(private readonly RepriceService $repriceService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON-файл с исправленными строками цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        $err = $output->getErrorOutput();

        if (!\is_file($file) || !\is_readable($file)) {
            $err->writeln(sprintf('Не удалось прочитать файл: %s', $file));

            return Command::FAILURE;
        }

        $json = \file_get_contents($file);
        if (false === $json) {
            $err->writeln(sprintf('Не удалось прочитать файл: %s', $file));

            return Command::FAILURE;
        }

        try {
            [$count, $delta] = $this->repriceService->reprice($json);
        } catch (\Throwable $e) {
            $err->writeln(sprintf('Ошибка коррекции: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $count));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $delta));

        return Command::SUCCESS;
    }
}
