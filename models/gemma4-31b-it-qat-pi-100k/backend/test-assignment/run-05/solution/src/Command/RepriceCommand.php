<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Price;
use App\Entity\UsageEvent;
use App\Service\BillingRateCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Provider;

#[AsCommand(name: 'app:billing:reprice')]
class RepriceCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager, 
        private BillingRateCalculator $calculator
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Reprices events based on corrected price file')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the correction JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');
        if (!file_exists($filePath)) {
            return Command::FAILURE;
        }

        $content = file_get_contents($filePath);
        $data = json_decode($content, true);
        if (!$data) return Command::FAILURE;

        $this->entityManager->beginTransaction();
        try {
            $repricedEventsCount = 0;
            $oldTotalCost = '0';
            $newTotalCost = '0';

            foreach ($data['prices'] ?? [] as $prData) {
                $validFrom = new \DateTimeImmutable($prData['valid_from']);
                $price = $this->entityManager->getRepository(Price::class)->findOneBy([
                    'providerCode' => $prData['provider'],
                    'modelCode' => $prData['model'],
                    'validFrom' => $validFrom
                ]);

                if (!$price) {
                    continue;
                }

                $price->setRates($prData['rates']);
                $this->entityManager->flush();

                $events = $this->entityManager->getRepository(UsageEvent::class)->findBy(['isRated' => true]);
                foreach ($events as $event) {
                    if ($event->getProvider() === $price->getProviderCode() && $event->getModel() === $price->getModelCode()) {
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
                        $currentPrice = $query->setMaxResults(1)->getOneOrNullResult();
                        
                        if ($currentPrice && $currentPrice->getId() === $price->getId()) {
                            $oldTotalCost = bcadd($oldTotalCost, $event->getCost() ?? '0', 6);
                            
                            $provider = $this->entityManager->getRepository(Provider::class)->findOneBy(['code' => $event->getProvider()]);
                            $newCost = $this->calculator->calculateCost($event, $price, $provider);
                            
                            $event->setCost($newCost);
                            $newTotalCost = bcadd($newTotalCost, $newCost, 6);
                            $repricedEventsCount++;
                        }
                    }
                }
            }
            
            $this->entityManager->commit();
            
            $delta = bcsub($newTotalCost, $oldTotalCost, 6);
            $output->writeln(sprintf("REPRICED_EVENTS=%d", $repricedEventsCount));
            $output->writeln(sprintf("REPRICE_DELTA_USD=%s", number_format((float)$delta, 6, '.', '')));
            
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
