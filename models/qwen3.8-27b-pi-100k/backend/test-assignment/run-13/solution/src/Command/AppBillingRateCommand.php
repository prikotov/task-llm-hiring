<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\RateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарифицирует все ещё не рассчитанные события',
)]
final class AppBillingRateCommand extends Command
{
    public function __construct(
        private readonly RateService $service,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ui = new SymfonyStyle($input, $output);

        try {
            $result = $this->service->rateAll();
        } catch (\Throwable $e) {
            $ui->error(sprintf('Ошибка тарификации: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        // Вывод в stdout: RATED=<n> UNRATED=<m>
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], $result['unrated']));

        // В stderr: unrated-события
        if ($result['unrated'] > 0) {
            foreach ($result['unratedEvents'] as $ev) {
                $output->getErrorOutput()->writeln(
                    sprintf('UNRATED %s/%s %s no_price', $ev['provider'], $ev['requestId'], $ev['ts'])
                );
            }
        }

        return Command::SUCCESS;
    }
}
