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

#[AsCommand(name: 'app:prices:import', description: 'Import prices from JSON file')]
class PricesImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON file path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_file($file)) {
            $output->getErrorOutput()->writeln("File not found: $file");
            return Command::FAILURE;
        }

        $content = file_get_contents($file);
        if ($content === false) {
            $output->getErrorOutput()->writeln("Cannot read file");
            return Command::FAILURE;
        }

        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $output->getErrorOutput()->writeln("Invalid JSON: " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->importProviders($data['providers'] ?? []);
        $this->importModels($data['models'] ?? []);
        $this->importPrices($data['prices'] ?? []);

        $this->em->flush();

        return Command::SUCCESS;
    }

    private function importProviders(array $providers): void
    {
        foreach ($providers as $p) {
            $repo = $this->em->getRepository(Provider::class);
            $provider = $repo->find($p['code']);
            if (!$provider) {
                $provider = new Provider();
                $provider->setCode($p['code']);
            }
            $provider->setName($p['name'] ?? '');
            $offPeak = $p['off_peak'] ?? null;
            if ($offPeak) {
                $provider->setOffPeakWindowStart($offPeak['window_start'] ?? null);
                $provider->setOffPeakWindowEnd($offPeak['window_end'] ?? null);
                $provider->setOffPeakMultiplier($offPeak['multiplier'] ?? null);
            } else {
                $provider->setOffPeakWindowStart(null);
                $provider->setOffPeakWindowEnd(null);
                $provider->setOffPeakMultiplier(null);
            }
            $this->em->persist($provider);
        }
    }

    private function importModels(array $models): void
    {
        foreach ($models as $m) {
            $provider = $m['provider'];
            $code = $m['code'];
            $repo = $this->em->getRepository(Model::class);
            $model = $repo->findOneBy(['providerCode' => $provider, 'code' => $code]);
            if (!$model) {
                $model = new Model();
                $model->setProviderCode($provider);
                $model->setCode($code);
                $this->em->persist($model);
            }
        }
    }

    private function importPrices(array $prices): void
    {
        foreach ($prices as $p) {
            $provider = $p['provider'];
            $model = $p['model'];
            $validFrom = new \DateTimeImmutable($p['valid_from']);
            $repo = $this->em->getRepository(Price::class);
            $price = $repo->findOneBy([
                'providerCode' => $provider,
                'modelCode' => $model,
                'validFrom' => $validFrom,
            ]);
            if (!$price) {
                $price = new Price();
                $price->setProviderCode($provider);
                $price->setModelCode($model);
                $price->setValidFrom($validFrom);
            }
            $price->setCurrency($p['currency'] ?? 'USD');
            $price->setUnit($p['unit'] ?? '');
            $rates = $p['rates'] ?? [];
            if ($p['unit'] === 'per_1m_tokens') {
                $price->setRateInput($rates['input'] ?? null);
                $price->setRateOutput($rates['output'] ?? null);
                $price->setRateCacheRead($rates['cache_read'] ?? null);
                $price->setRateCacheWrite($rates['cache_write'] ?? null);
                $price->setRateAny(null);
            } else {
                $price->setRateAny($rates['any'] ?? null);
                $price->setRateInput(null);
                $price->setRateOutput(null);
                $price->setRateCacheRead(null);
                $price->setRateCacheWrite(null);
            }
            $this->em->persist($price);
        }
    }
}
