<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Exception\BillingException;
use App\Application\Service\UsageImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Импорт событий использования из JSONL-файла (одна строка — одно событие)',
)]
final class AppUsageImportCommand extends Command
{
    public function __construct(
        private readonly UsageImportService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');
        if (!is_file($file) || !is_readable($file)) {
            $output->getErrorOutput()->writeln(sprintf('<error>не найден или не читается файл: %s</error>', $file));
            return Command::FAILURE;
        }

        try {
            $this->service->import($file);
        } catch (BillingException $e) {
            $output->getErrorOutput()->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return Command::FAILURE;
        } catch (\Throwable $e) {
            $output->getErrorOutput()->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return Command::FAILURE;
        }

        $io->success('Импорт usage выполнен.');
        return Command::SUCCESS;
    }
}
