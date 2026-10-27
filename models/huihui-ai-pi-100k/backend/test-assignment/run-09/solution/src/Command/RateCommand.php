<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\RaterService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарифицировать все ещё не рассчитанные события (повторяет попытки unrated)',
)]
final class RateCommand extends Command
{
    public function __construct(private readonly RaterService $rater)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->rater->run();

            foreach ($result['unrated'] as $event) {
                $output->getErrorOutput()->writeln(sprintf(
                    'UNRATED %s/%s %s no_price',
                    $event->getProviderCode(),
                    $event->getProviderRequestId(),
                    $event->getTs()->format('Y-m-d\TH:i:s\Z'),
                ));
            }
            $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], count($result['unrated'])));
        } catch (\Exception $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
