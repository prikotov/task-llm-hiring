<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RateProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарифицировать все ещё не рассчитанные события (повторные попытки unrated включены)',
)]
final class BillingRateCommand extends BaseCommand
{
    public function __construct(
        private readonly RateProcessor $processor,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $this->withErrorHandling($input, $output, function () use ($input, $output): int {
            $result = $this->processor->ratePending();

            foreach ($result['unrated'] as $event) {
                $output->getErrorOutput()->writeln(sprintf(
                    'UNRATED %s/%s %s no_price',
                    $event['provider'],
                    $event['provider_request_id'],
                    $event['ts'],
                ));
            }

            $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], \count($result['unrated'])));
            return self::SUCCESS;
        });
    }
}
