<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\UsageEvent;
use App\Service\CostCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate', description: 'Rate unrated usage events')]
class BillingRateCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private CostCalculator $calculator
    ) { parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = $this->em->getRepository(UsageEvent::class);
        $events = $repo->createQueryBuilder('e')
            ->where('e.cost IS NULL')
            ->getQuery()
            ->getResult();

        $ratedCount = 0;
        $unratedCount = 0;

        foreach ($events as $event) {
            /** @var UsageEvent $event */
            $price = $this->findApplicablePrice($event);
            if (!$price) {
                $unratedCount++;
                $output->getErrorOutput()->writeln(sprintf(
                    'UNRATED %s/%s %s no_price',
                    $event->getProviderCode(),
                    $event->getProviderRequestId(),
                    $event->getTs()->format('c')
                ));
                continue;
            }
            $providerRepo = $this->em->getRepository(\App\Entity\Provider::class);
            $provider = $providerRepo->findOneBy(['code' => $event->getProviderCode()]);
            if (!$provider) {
                $unratedCount++;
                $output->getErrorOutput()->writeln(sprintf(
                    'UNRATED %s/%s %s no_provider',
                    $event->getProviderCode(),
                    $event->getProviderRequestId(),
                    $event->getTs()->format('c')
                ));
                continue;
            }
            $cost = $this->calculator->calculate($event, $price, $provider);
            $event->setCost($cost);
            $ratedCount++;
        }

        $this->em->flush();

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $ratedCount, $unratedCount));

        return Command::SUCCESS;
    }

    private function findApplicablePrice(UsageEvent $event)
    {
        $priceRepo = $this->em->getRepository(\App\Entity\Price::class);
        $qb = $priceRepo->createQueryBuilder('p')
            ->where('p.providerCode = :provider')
            ->andWhere('p.modelCode = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('provider', $event->getProviderCode())
            ->setParameter('model', $event->getModelCode())
            ->setParameter('ts', $event->getTs())
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }
}
