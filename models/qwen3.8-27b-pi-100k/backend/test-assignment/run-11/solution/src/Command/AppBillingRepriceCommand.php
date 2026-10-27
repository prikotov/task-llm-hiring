<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Exception\BillingException;
use App\Application\Service\RepriceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция цены (upsert цен по файлу + пересчёт событий)',
)]
final class AppBillingRepriceCommand extends Command
{
    public function __construct(
        private readonly RepriceService $service,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с секцией prices');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        if (!is_file($file) || !is_readable($file)) {
            $output->getErrorOutput()->writeln(sprintf('<error>не найден или не читается файл: %s</error>', $file));
            return Command::FAILURE;
        }
        $raw = file_get_contents($file);
        if ($raw === false) {
            $output->getErrorOutput()->writeln('<error>не удалось прочитать файл</error>');
            return Command::FAILURE;
        }
        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $output->getErrorOutput()->writeln(sprintf('<error>некорректный JSON: %s</error>', $e->getMessage()));
            return Command::FAILURE;
        }
        if (!is_array($data)) {
            $output->getErrorOutput()->writeln('<error>ожидается JSON-объект с секцией prices</error>');
            return Command::FAILURE;
        }

        $err = $output->getErrorOutput();
        try {
            $conn = $this->em->getConnection();
            $conn->beginTransaction();
            $stats = $this->service->reprice($data);
            $conn->commit();
        } catch (BillingException $e) {
            if ($conn->isTransactionActive()) {
                $conn->rollBack();
            }
            $err->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return Command::FAILURE;
        } catch (\Throwable $e) {
            if ($conn->isTransactionActive()) {
                $conn->rollBack();
            }
            $err->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return Command::FAILURE;
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $stats['n']));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $stats['deltaUsd']->format(6)));
        return Command::SUCCESS;
    }
}
