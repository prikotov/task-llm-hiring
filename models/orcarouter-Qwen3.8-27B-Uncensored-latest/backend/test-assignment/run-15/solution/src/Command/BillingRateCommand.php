<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UsageEventRepository;
use App\Service\RatingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Rate all pending usage events and retry unrated ones.')]
final class BillingRateCommand extends Command
{
    public function __construct(
        private readonly RatingService $rating,
        private readonly UsageEventRepository $events,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stdout = $output;
        $stderr = $output->getErrorOutput();

        $em = $this->em;
        $candidats = [];
        foreach ($this->events->findBy([
            'status' => \App\Domain\UsageEvent::STATUS_PENDING,
        ]) as $e) {
            $candidats[$e->id()] = $e;
        }
        foreach ($this->events->findBy([
            'status' => \App\Domain\UsageEvent::STATUS_UNRATED,
        ]) as $e) {
            $candidats[$e->id()] = $e;
        }

        $rated = 0;
        $unrated = 0;
        foreach ($candidats as $event) {
            $r = $this->rating->rateEvent($event);
            if ($r === null) {
                $event->markUnrated();
                $unrated++;
                $stderr->writeln(sprintf(
                    'UNRATED %s/%s %s no_price',
                    $event->providerCode(),
                    $event->providerRequestId(),
                    $event->tsUtc()->format('c'),
                ));
                continue;
            }
            $costStr = $this->rating->eventCostString($r['cost']);
            $event->rate($r['line'], $costStr);
            $rated++;
        }
        $em->flush();
        $stdout->writeln(sprintf('RATED=%d UNRATED=%d', $rated, $unrated));
        return 0;
    }
}
