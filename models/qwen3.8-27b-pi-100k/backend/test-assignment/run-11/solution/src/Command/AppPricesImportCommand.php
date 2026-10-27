<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Exception\BillingException;
use App\Application\Service\PriceBookService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импорт справочника цен (providers/models/prices) из JSON-файла',
)]
final class AppPricesImportCommand extends Command
{
    public function __construct(
        private readonly PriceBookService $service,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с providers/models/prices');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
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
            $output->getErrorOutput()->writeln('<error>ожидается JSON-объект с providers[]/models[]/prices[]</error>');
            return Command::FAILURE;
        }

        $em = $this->em;
        try {
            $conn = $em->getConnection();
            $conn->beginTransaction();
            $this->service->import($data);
            $conn->commit();
        } catch (BillingException $e) {
            if ($conn->isTransactionActive()) {
                $conn->rollBack();
            }
            $output->getErrorOutput()->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return Command::FAILURE;
        } catch (\Throwable $e) {
            if ($conn->isTransactionActive()) {
                $conn->rollBack();
            }
            $output->getErrorOutput()->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return Command::FAILURE;
        }

        $io->success('Импорт цен выполнен.');
        return Command::SUCCESS;
    }
}
