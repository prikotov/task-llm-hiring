<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Model;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Импорт событий использования LLM'
)]
final class UsageImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSONL-файлу с событиями');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_file($file)) {
            $output->writeln('<error>Файл не найден: ' . $file . '</error>');
            return Command::FAILURE;
        }

        $handle = fopen($file, 'r');
        if ($handle === false) {
            $output->writeln('<error>Не удалось открыть файл: ' . $file . '</error>');
            return Command::FAILURE;
        }

        $this->em->beginTransaction();
        $lineNo = 0;
        try {
            while (($line = fgets($handle)) !== false) {
                $lineNo++;
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                $data = json_decode($line, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $output->writeln('<error>Некорректный JSONL на строке ' . $lineNo . ': ' . json_last_error_msg() . '</error>');
                    $this->em->rollback();
                    fclose($handle);
                    return Command::FAILURE;
                }

                $provider = $this->em->getRepository(Provider::class)->find($data['provider']);
                if (!$provider) {
                    $output->writeln('<error>Провайдер не найден: ' . $data['provider'] . ' (строка ' . $lineNo . ')</error>');
                    $this->em->rollback();
                    fclose($handle);
                    return Command::FAILURE;
                }

                $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $data['model']]);
                if (!$model) {
                    $output->writeln('<error>Модель не найдена: ' . $data['provider'] . '/' . $data['model'] . ' (строка ' . $lineNo . ')</error>');
                    $this->em->rollback();
                    fclose($handle);
                    return Command::FAILURE;
                }

                $existing = $this->em->getRepository(UsageEvent::class)->findOneBy([
                    'provider' => $provider,
                    'providerRequestId' => $data['provider_request_id'],
                ]);
                if ($existing) {
                    continue; // идемпотентность
                }

                $ts = new \DateTimeImmutable($data['ts']);
                $event = new UsageEvent(
                    $provider,
                    $data['provider_request_id'],
                    $model,
                    $data['user'],
                    $ts,
                    (int)$data['tokens_input'],
                    (int)$data['tokens_output'],
                    (int)$data['tokens_cache_read'],
                    (int)$data['tokens_cache_write']
                );
                $this->em->persist($event);
            }
            fclose($handle);
            $this->em->flush();
            $this->em->commit();
            $output->writeln('<info>События успешно импортированы</info>');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->em->rollback();
            $output->writeln('<error>Ошибка импорта: ' . $e->getMessage() . '</error>');
            fclose($handle);
            return Command::FAILURE;
        }
    }
}
