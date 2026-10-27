<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\BillingService;
use App\Domain\ImportDataException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:reprice', description: 'Ретро-коррекция цен (исправленная строка)')]
final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly BillingService $service,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с исправленной строкой цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $json = file_get_contents($file);
            $data = json_decode($json ?: '', true);
        } catch (\Exception $e) {
            $output->getErrorOutput()->writeln('Ошибка чтения файла: ' . $e->getMessage());
            return 1;
        }

        if (!is_array($data)) {
            $output->getErrorOutput()->writeln('Ошибка: некорректный JSON');
            return 1;
        }

        try {
            $result = $this->service->reprice($data);
        } catch (ImportDataException $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());
            return 1;
        } catch (\Exception $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());
            return 1;
        }

        $this->em->flush();

        $output->writeln('REPRICED_EVENTS=' . $result['reprice_events']);
        $output->writeln('REPRICE_DELTA_USD=' . $result['delta']->toFixedSix());

        return 0;
    }
}
