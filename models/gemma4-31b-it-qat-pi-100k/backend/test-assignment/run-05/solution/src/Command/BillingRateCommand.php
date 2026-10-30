<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Service\BillingRateCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate')]
class BillingRateCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BillingRateCalculator $calculator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Rates unrated usage events');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $unratedEvents = $this->entityManager->getRepository(UsageEvent::class)->findBy([], ['ts' => 'ASC']);
        $unratedEvents = array_filter($unratedEvents, fn($e) => !$e->isRated());

        $ratedCount = 0;
        $unratedCount = 0;

        foreach ($unratedEvents as $event) {
            $provider = $this->entityManager->getRepository(Provider::class)->findOneBy(['code' => $event->getProvider()]);
            
            $query = $this->entityManager->createQuery(
                'SELECT p FROM App\Entity\Price p 
                 WHERE p.providerCode = :provider 
                 AND p.modelCode = :model 
                 AND p.validFrom <= :ts 
                 ORDER BY p.validFrom DESC'
            )->setParameters([
                'provider' => $event->getProvider(),
                'model' => $event->getModel(),
                'ts' => $event->getTs(),
            ]);
            
            $price = $query->setMaxResults(1)->getOneOrNullResult();

            if (!$price) {
                fwrite(STDERR, sprintf("UNRATED %s/%s %s no_price\n", $event->getProvider(), $event->getProviderRequestId(), $event->getTs()->format(\DateTimeInterface::ATOM)));
                $unratedCount++;
                continue;
            }

            $cost = $this->calculator->calculateCost($event, $price, $provider);
            $event->setCost($cost);
            $event->setRated(true);
            $ratedCount++;
        }

        $this->entityManager->flush();
        
        $output->writeln(sprintf("RATED=%d UNRATED=%d", $ratedCount, $unratedCount));

        return Command::SUCCESS;
    }
}
