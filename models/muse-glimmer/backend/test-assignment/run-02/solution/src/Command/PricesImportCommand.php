<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\Model;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:prices:import', description: 'Import prices JSON file')]
class PricesImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to JSON file');
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
        $data = json_decode($content, true);
        if ($data === null) {
            $output->getErrorOutput()->writeln("Invalid JSON");
            return Command::FAILURE;
        }

        // Providers
        foreach ($data['providers'] ?? [] as $p) {
            $provider = $this->em->find(Provider::class, $p['code']);
            if (!$provider) {
                $provider = new Provider();
                $provider->setCode($p['code']);
            }
            $provider->setName($p['name'] ?? '');
            $off = $p['off_peak'] ?? null;
            if ($off) {
                $provider->setOffPeakWindowStart($off['window_start'] ?? null);
                $provider->setOffPeakWindowEnd($off['window_end'] ?? null);
                $provider->setOffPeakMultiplier($off['multiplier'] ?? null);
            } else {
                $provider->setOffPeakWindowStart(null);
                $provider->setOffPeakWindowEnd(null);
                $provider->setOffPeakMultiplier(null);
            }
            $this->em->persist($provider);
        }

        // Models
        foreach ($data['models'] ?? [] as $m) {
            $providerCode = $m['provider'];
            $code = $m['code'];
            $repo = $this->em->getRepository(Model::class);
            $existing = $repo->findOneBy(['providerCode' => $providerCode, 'code' => $code]);
            if (!$existing) {
                $model = new Model();
                $model->setProviderCode($providerCode);
                $model->setCode($code);
                $this->em->persist($model);
            }
        }

        // Prices
        foreach ($data['prices'] ?? [] as $pr) {
            $provider = $pr['provider'];
            $model = $pr['model'];
            $validFrom = new \DateTimeImmutable($pr['valid_from']);
            $repo = $this->em->getRepository(Price::class);
            $existing = $repo->findOneBy([
                'providerCode' => $provider,
                'modelCode' => $model,
                'validFrom' => $validFrom,
            ]);
            if ($existing) {
                continue; // idempotent
            }
            $price = new Price();
            $price->setProviderCode($provider);
            $price->setModelCode($model);
            $price->setValidFrom($validFrom);
            $price->setCurrency($pr['currency'] ?? 'USD');
            $price->setUnit($pr['unit'] ?? '');
            $rates = $pr['rates'] ?? [];
            if ($pr['unit'] === 'per_1m_tokens') {
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

        $this->em->flush();
        return Command::SUCCESS;
    }
}
