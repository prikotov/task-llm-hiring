<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\RatingService;
use App\Domain\Billing\Iso8601;
use App\Domain\Exception\BillingException;
use App\Entity\UsageEvent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Рассчитать стоимость всех событий, ожидающих тарификации (успешно и при наличии нетарифицированных)',
)]
final class RateCommand extends Command
{
    public function __construct(private readonly RatingService $rater)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->rater->rate();
        } catch (BillingException $exception) {
            $output->getErrorOutput()->writeln('Ошибка: '.$exception->getMessage());
            return 1;
        }

        // Строки о недорассчитанных событиях — только в stderr:
        // stdout обязан содержать ровно один выводимый лог.
        $unrated = $result->unratedEvents;
        usort($unrated, static fn (UsageEvent $a, UsageEvent $b): int =>
            [$a->providerCode(), $a->providerRequestId()] <=> [$b->providerCode(), $b->providerRequestId()]
        );
        foreach ($unrated as $event) {
            $output->getErrorOutput()->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->providerCode(),
                $event->providerRequestId(),
                Iso8601::formatUtc($event->ts()),
            ));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result->rated, $result->unrated));

        return 0;
    }
}
