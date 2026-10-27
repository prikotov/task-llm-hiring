<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RepriceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция цен: применяет исправленные строки и пересчитывает события',
)]
final class RepriceCommand extends AppCommand
{
    public function __construct(private RepriceService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу коррекции (формат цен)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');

        try {
            $result = $this->service->reprice($path);
        } catch (\Throwable $e) {
            return $this->fail($output, $e);
        }

        $output->writeln('REPRICED_EVENTS=' . $result['events']);
        $output->writeln('REPRICE_DELTA_USD=' . $result['delta']);

        return self::SUCCESS;
    }
}
