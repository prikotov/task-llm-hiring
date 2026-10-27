<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Rater;
use App\Repository\PriceRowRepository;
use App\Repository\ProviderRepository;
use App\Repository\RatingRepository;
use App\Repository\UsageEventRowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RateCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRowRepository $events,
        private readonly PriceRowRepository $prices,
        private readonly RatingRepository $ratings,
        private readonly ProviderRepository $providers,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:billing:rate')
            ->setDescription('Tariff all not-yet-rated events (re-evaluates previous unrated)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rater = new Rater($this->providers->allAsArray());

        // все не-оценённые события. Для каждого — latest-строка с valid_from ≤ ts.
        $pending = $this->em->createQuery(
            'SELECT e FROM App\Entity\UsageEventRow e
             WHERE NOT EXISTS (SELECT 1 FROM App\Entity\Rating r WHERE r.eventId = e.id)
             ORDER BY e.id ASC'
        )->getResult();

        $rated = 0;
        $unrated = 0;

        foreach ($pending as $e) {
            $best = $this->prices->findLatestUpTo($e->provider, $e->model, $e->ts);
            if ($best === null) {
                $unrated++;
                $output->getErrorOutput()->writeln(
                    sprintf('UNRATED %s/%s %s no_price', $e->provider, $e->providerRequestId, $e->ts->format('Y-m-d\TH:i:s\Z'))
                );
                continue;
            }

            $cost = $rater->costRounded($e, $best);
            $this->ratings->markRated((int) $e->id, (int) $best->id, $cost);
            $this->em->flush();
            $rated++;
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $rated, $unrated));
        return 0;
    }
}
