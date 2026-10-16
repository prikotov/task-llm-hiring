<?php

declare(strict_types=1);

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

#[AsCommand(name: 'app:prices:import', description: 'Import providers, models and prices from JSON file')]
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
            $output->writeln("<error>File not found: $file</error>", OutputInterface::VERBOSITY_NORMAL);
            return Command::FAILURE;
        }

        $content = file_get_contents($file);
        if ($content === false) {
            $output->writeln("<error>Cannot read file</error>");
            return Command::FAILURE;
        }

        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $output->writeln("<error>Invalid JSON</error>");
            return Command::FAILURE;
        }

        $providers = $data['providers'] ?? [];
        foreach ($providers as $p) {
            $code = $p['code'];
            $provider = $this->em->find(Provider::class, $code);
            if (!$provider) {
                $provider = new Provider($code, $p['name'] ?? '');
            } else {
                $provider->setName($p['name'] ?? $provider->getName());
            }
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

        $models = $data['models'] ?? [];
        foreach ($models as $m) {
            $providerCode = $m['provider'];
            $code = $m['code'];
            $repo = $this->em->getRepository(Model::class);
            $existing = $repo->findOneBy(['providerCode' => $providerCode, 'code' => $code]);
            if (!$existing) {
                $model = new Model($providerCode, $code);
                $this->em->persist($model);
            }
        }

        $prices = $data['prices'] ?? [];
        foreach ($prices as $pr) {
            $providerCode = $pr['provider'];
            $modelCode = $pr['model'];
            $validFromStr = $pr['valid_from'];
            $validFrom = new \DateTimeImmutable($validFromStr);
            $currency = $pr['currency'] ?? 'USD';
            $unit = $pr['unit'] ?? '';
            $repo = $this->em->getRepository(Price::class);
            $existing = $repo->findOneBy([
                'providerCode' => $providerCode,
                'modelCode' => $modelCode,
                'validFrom' => $validFrom,
            ]);
            if (!$existing) {
                $price = new Price($providerCode, $modelCode, $validFrom, $currency, $unit);
            } else {
                $price = $existing;
            }
            $rates = $pr['rates'] ?? [];
            if ($unit === 'per_1m_tokens') {
                $price->setRateInput($rates['input'] ?? null);
                $price->setRateOutput($rates['output'] ?? null);
                $price->setRateCacheRead($rates['cache_read'] ?? null);
                $price->setRateCacheWrite($rates['cache_write'] ?? null);
                $price->setRateAny(null);
            } elseif ($unit === 'per_1m_tokens_unified') {
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
