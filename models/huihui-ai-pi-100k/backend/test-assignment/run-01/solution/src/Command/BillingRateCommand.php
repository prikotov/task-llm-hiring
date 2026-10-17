<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\UsageEvent;
use App\Service\RatingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Тарификация ещё не рассчитанных событий (повторные попытки для unrated)',
)]
final class BillingRateCommand extends Command
{
    public function __construct(
        private readonly RatingService $service,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->service->rateUnrated($this->em);

        foreach ($result->unrated as $event) {
            // служебные сообщения — в stderr
            $output->getErrorOutput()->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->provider->code,
                $event->providerRequestId,
                $event->occurredAtIso()
            ));
        }

        // stdout — ровно одна строка-сводка
        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result->rated, \count($result->unrated)));

        return Command::SUCCESS;
    }
}
