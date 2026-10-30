<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Prices\PriceImportService;
use App\Domain\ImportDataException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import', description: 'Импорт каталога цен (JSON-файл)')]
final class PricesImportCommand extends Command
{
    public function __construct(
        private readonly PriceImportService $service,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        $output->getErrorOutput()->writeln('Импорт цен из ' . $file . '...');

        try {
            $data = json_decode(file_get_contents($file) ?: '', true);
            $this->service->import($data);
            $this->em->flush();
        } catch (ImportDataException $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());
            return 1;
        }

        $output->getErrorOutput()->writeln('Готово.');
        return 0;
    }
}
