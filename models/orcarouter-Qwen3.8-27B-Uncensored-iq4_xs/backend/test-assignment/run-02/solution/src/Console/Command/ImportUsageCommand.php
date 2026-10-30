<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Domain\Common\ImportFormatException;
use App\Domain\Usage\UsageImporter;
use App\Domain\Usage\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * app:usage:import <file> — загрузка событий использования (JSONL).
 * Повторная доставка уже известных событий игнорируется (код 0).
 */
#[AsCommand(
    name: 'app:usage:import',
    description: 'Импорт событий использования (JSONL, дедупликация по (provider, provider_request_id))',
)]
final class ImportUsageCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageImporter $importer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу с событиями');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');

        $real = realpath($file);
        if ($real === false) {
            $io->error('Файл не найден: '.$file);

            return Command::FAILURE;
        }

        try {
            $result = $this->importer->importFile($real);
        } catch (ImportFormatException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->writeln(sprintf(
            'События: %d принято, %d дубликат(ов) пропущено (%s)',
            $result['imported'],
            $result['duplicates'],
            $file,
        ));

        return Command::SUCCESS;
    }
}
