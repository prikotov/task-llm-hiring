<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Usage\UsageEvent;
use App\Import\ImportException;
use App\Import\UsageEventParser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class UsageImportCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:usage:import')
            ->setDescription('Импорт событий использования из JSONL-файла (дедупликация по (provider, provider_request_id))')
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу с событиями');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');

        if (!is_file($file) || !is_readable($file)) {
            $io->error('Файл не найден или нечитаем: '.$file);

            return Command::FAILURE;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            $io->error('Не удалось прочитать файл: '.$file);

            return Command::FAILURE;
        }

        $new = 0;
        $duplicates = 0;
        $repo = $this->em->getRepository(UsageEvent::class);

        foreach ($lines as $num => $line) {
            try {
                $event = UsageEventParser::parseLine((string) $line);
            } catch (ImportException $e) {
                $io->error("Строка ".($num + 1).": ".$e->getMessage());

                return Command::FAILURE;
            }

            $existing = $repo->findOneBy([
                'provider' => $event['provider'],
                'providerRequestId' => $event['providerRequestId'],
            ]);

            if ($existing !== null) {
                // дубль — игнорируем целиком (без обновлений/доначислений)
                $duplicates++;

                continue;
            }

            $usage = new UsageEvent();
            $usage->setProvider($event['provider']);
            $usage->setProviderRequestId($event['providerRequestId']);
            $usage->setModel($event['model']);
            $usage->setUser($event['user']);
            $usage->setTs($event['ts']);
            $usage->setTokens($event['input'], $event['output'], $event['cacheRead'], $event['cacheWrite']);
            $usage->setStatus(UsageEvent::STATUS_UNRATED);

            $this->em->persist($usage);
            $new++;
        }

        $this->em->flush();

        $io->success(sprintf('Импортировано событий: %d, проигнорировано дублей: %d', $new, $duplicates));

        return Command::SUCCESS;
    }
}
