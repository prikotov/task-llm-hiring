<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Usage\UsageImportService;
use App\Domain\ImportDataException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Импорт событий использования (JSONL-файл)')]
final class UsageImportCommand extends Command
{
    public function __construct(
        private readonly UsageImportService $service,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу событий');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        $output->getErrorOutput()->writeln('Импорт событий из ' . $file . '...');

        try {
            $imported = $this->service->import($file);
            $this->em->flush();
        } catch (ImportDataException $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());
            return 1;
        }

        $output->getErrorOutput()->writeln("Импортировано: {$imported}");
        return 0;
    }
}
