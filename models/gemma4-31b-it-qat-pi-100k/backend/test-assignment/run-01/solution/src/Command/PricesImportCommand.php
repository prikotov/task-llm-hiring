<?php

namespace App\Command;

use App\Entity\Model;
use App\Entity\Price;
use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:prices:import', description: 'Import prices from JSON file')]
class PricesImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the prices JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            $io->error("File not found: $filePath");
            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($filePath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $io->error("Invalid JSON in file: " . json_last_error_msg());
            return Command::FAILURE;
        }

        try {
            $this->importProviders($data['providers'] ?? []);
            $this->entityManager->flush();
            $this->importModels($data['models'] ?? []);
            $this->entityManager->flush();
            $this->importPrices($data['prices'] ?? []);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $io->error("An error occurred during import: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function importProviders(array $providers): void
    {
        foreach ($providers as $pData) {
            $provider = $this->entityManager->getRepository(Provider::class)->find($pData['code']);
            if (!$provider) {
                $provider = new Provider();
                $provider->code = $pData['code'];
            }
            $provider->name = $pData['name'];
            
            $offPeak = $pData['off_peak'] ?? null;
            if ($offPeak) {
                $provider->offPeakWindowStart = $offPeak['window_start'];
                $provider->offPeakWindowEnd = $offPeak['window_end'];
                $provider->offPeakMultiplier = $offPeak['multiplier'];
            } else {
                $provider->offPeakWindowStart = null;
                $provider->offPeakWindowEnd = null;
                $provider->offPeakMultiplier = null;
            }
            
            $this->entityManager->persist($provider);
        }
    }

    private function importModels(array $models): void
    {
        foreach ($models as $mData) {
            $provider = $this->entityManager->getRepository(Provider::class)->find($mData['provider']);
            if (!$provider) {
                // Should not happen if providers are imported first, but just in case
                throw new \RuntimeException("Provider {$mData['provider']} not found");
            }

            $model = $this->entityManager->getRepository(Model::class)->findOneBy([
                'provider' => $provider,
                'code' => $mData['code'],
            ]);

            if (!$model) {
                $model = new Model();
                $model->provider = $provider;
                $model->code = $mData['code'];
                $this->entityManager->persist($model);
            }
        }
    }

    private function importPrices(array $prices): void
    {
        foreach ($prices as $pData) {
            $provider = $this->entityManager->getRepository(Provider::class)->find($pData['provider']);
            $model = $this->entityManager->getRepository(Model::class)->findOneBy([
                'provider' => $provider,
                'code' => $pData['model'],
            ]);

            if (!$model) {
                throw new \RuntimeException("Model {$pData['provider']}/{$pData['model']} not found");
            }

            $validFrom = new \DateTimeImmutable($pData['valid_from']);
            
            $price = $this->entityManager->getRepository(Price::class)->findOneBy([
                'model' => $model,
                'validFrom' => $validFrom,
            ]);

            if (!$price) {
                $price = new Price();
                $price->model = $model;
                $price->validFrom = $validFrom;
                $this->entityManager->persist($price);
            }

            $price->currency = $pData['currency'];
            $price->unit = $pData['unit'];
            
            $rates = $pData['rates'] ?? [];
            $price->rateInput = $rates['input'] ?? null;
            $price->rateOutput = $rates['output'] ?? null;
            $price->rateCacheRead = $rates['cache_read'] ?? null;
            $price->rateCacheWrite = $rates['cache_write'] ?? null;
            $price->rateAny = $rates['any'] ?? null;
        }
    }
}
