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

#[AsCommand(name: 'app:prices:import', description: 'Import providers, models and prices')]
final class PricesImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON file with prices');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_file($file)) {
            $output->writeln('<error>File not found</error>', OutputInterface::VERBOSITY_QUIET);
            return Command::FAILURE;
        }

        $content = file_get_contents($file);
        if ($content === false) {
            return Command::FAILURE;
        }

        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return Command::FAILURE;
        }

        $providers = $data['providers'] ?? [];
        foreach ($providers as $p) {
            $code = $p['code'];
            $provider = $this->em->find(Provider::class, $code);
            if (!$provider) {
                $provider = new Provider($code, $p['name'] ?? '');
            } else {
                // update name maybe
                // name not mutable in entity? We'll just keep current.
            }
            // off_peak
            $offPeak = $p['off_peak'] ?? null;
            if ($offPeak === null) {
                $provider->setOffPeakStart(null);
                $provider->setOffPeakEnd(null);
                $provider->setOffPeakMultiplier(null);
            } else {
                $start = \DateTime::createFromFormat('H:i', $offPeak['window_start']);
                $end = \DateTime::createFromFormat('H:i', $offPeak['window_end']);
                $provider->setOffPeakStart($start ?: null);
                $provider->setOffPeakEnd($end ?: null);
                $provider->setOffPeakMultiplier($offPeak['multiplier'] ?? null);
            }
            $this->em->persist($provider);
        }

        $models = $data['models'] ?? [];
        foreach ($models as $m) {
            $provider = $m['provider'];
            $code = $m['code'];
            $repo = $this->em->getRepository(Model::class);
            $existing = $repo->findOneBy(['providerCode' => $provider, 'code' => $code]);
            if (!$existing) {
                $model = new Model($provider, $code);
                $this->em->persist($model);
            }
        }

        $prices = $data['prices'] ?? [];
        foreach ($prices as $pr) {
            $provider = $pr['provider'];
            $model = $pr['model'];
            $validFrom = new \DateTimeImmutable($pr['valid_from']);
            $repo = $this->em->getRepository(Price::class);
            $existing = $repo->findOneBy([
                'providerCode' => $provider,
                'modelCode' => $model,
                'validFrom' => $validFrom,
            ]);
            if (!$existing) {
                $price = new Price($provider, $model, $validFrom, $pr['currency'] ?? 'USD', $pr['unit'] ?? '');
                $this->setRates($price, $pr);
                $this->em->persist($price);
            } else {
                // upsert rates
                $this->setRates($existing, $pr);
                $this->em->persist($existing);
            }
        }

        $this->em->flush();

        return Command::SUCCESS;
    }

    private function setRates(Price $price, array $pr): void
    {
        $rates = $pr['rates'] ?? [];
        $unit = $pr['unit'] ?? '';
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
    }
}
