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
    description: 'Ретро-коррекция цены: применить исправленные строки и пересчитать затронутые события (бонус)',
)]
final class BillingRepriceCommand extends BaseCommand
{
    public function __construct(
        private readonly RepriceService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с исправленными строками цен (раздел prices)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        return $this->withErrorHandling($input, $output, function () use ($file, $input, $output): int {
            $result = $this->service->reprice($file);
            $output->writeln(sprintf('REPRICED_EVENTS=%d', $result['events']));
            $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result['delta']));
            return self::SUCCESS;
        });
    }
}
