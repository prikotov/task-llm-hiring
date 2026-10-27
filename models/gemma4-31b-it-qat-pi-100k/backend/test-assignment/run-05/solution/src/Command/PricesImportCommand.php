<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Provider;
use App\Entity\Model;
use App\Entity\Price;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import')]
class PricesImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Imports providers, models and prices from JSON file')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the prices JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');
        if (!file_exists($filePath)) {
            $output->writeln("File not found: $filePath", OutputInterface::VERBOSITY_VERBOSE);
            return Command::FAILURE;
        }

        $content = file_get_contents($filePath);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return Command::FAILURE;
        }

        $this->entityManager->beginTransaction();
        try {
            foreach ($data['providers'] ?? [] as $pData) {
                $provider = $this->entityManager->getRepository(Provider::class)->findOneBy(['code' => $pData['code']]);
                if (!$provider) {
                    $provider = new Provider($pData['code'], $pData['name'], $pData['off_peak']);
                    $this->entityManager->persist($provider);
                } else {
                    $provider->setName($pData['name']);
                    $provider->setOffPeak($pData['off_peak']);
                }
            }

            foreach ($data['models'] ?? [] as $mData) {
                $model = $this->entityManager->getRepository(Model::class)->findOneBy([
                    'providerCode' => $mData['provider'],
                    'code' => $mData['code']
                ]);
                if (!$model) {
                    $model = new Model($mData['provider'], $mData['code']);
                    $this->entityManager->persist($model);
                }
            }

            foreach ($data['prices'] ?? [] as $prData) {
                $validFrom = new \DateTimeImmutable($prData['valid_from']);
                $price = $this->entityManager->getRepository(Price::class)->findOneBy([
                    'providerCode' => $prData['provider'],
                    'modelCode' => $prData['model'],
                    'validFrom' => $validFrom
                ]);

                if (!$price) {
                    $price = new Price(
                        $prData['provider'],
                        $prData['model'],
                        $validFrom,
                        $prData['currency'],
                        $prData['unit'],
                        $prData['rates']
                    );
                    $this->entityManager->persist($price);
                } else {
                    $price->setRates($prData['rates']);
                }
            }

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $output->writeln("Error importing prices: " . $e->getMessage(), OutputInterface::VERBOSITY_VERBOSE);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
