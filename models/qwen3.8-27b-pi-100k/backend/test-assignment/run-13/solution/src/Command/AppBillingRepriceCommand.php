<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\RepriceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция цены (bonus): upsert строки цен + пересчёт событий',
)]
final class AppBillingRepriceCommand extends Command
{
    public function __construct(
        private readonly RepriceService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Файл с коррекцией цен (JSON)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $file */
        $file = $input->getArgument('file');
        $ui = new SymfonyStyle($input, $output);

        try {
            $result = $this->service->repriceFromFile($file);
        } catch (\Throwable $e) {
            $ui->error(sprintf('Ошибка коррекции: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        // В stdout — ровно две строки
        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result['events']));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result['delta']));

        return Command::SUCCESS;
    }
}
