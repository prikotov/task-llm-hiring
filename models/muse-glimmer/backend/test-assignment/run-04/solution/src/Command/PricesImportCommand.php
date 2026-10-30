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

#[AsCommand(name: 'app:prices:import')]
class PricesImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em) { parent::__construct(); }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSON file path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('file');
        if (!is_file($path)) {
            $output->getErrorOutput()->writeln('File not found');
            return Command::FAILURE;
        }
        $content = file_get_contents($path);
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $output->getErrorOutput()->writeln('Invalid JSON');
            return Command::FAILURE;
        }

        foreach ($data['providers'] ?? [] as $p) {
            $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $p['code']]) ?? new Provider();
            $provider->setCode($p['code']);
            $provider->setName($p['name'] ?? '');
            $off = $p['off_peak'] ?? null;
            if ($off) {
                $provider->setOffPeakStart($off['window_start'] ?? null);
                $provider->setOffPeakEnd($off['window_end'] ?? null);
                $provider->setOffPeakMultiplier($off['multiplier'] ?? null);
            } else {
                $provider->setOffPeakStart(null);
                $provider->setOffPeakEnd(null);
                $provider->setOffPeakMultiplier(null);
            }
            // upsert by code
            $this->em->persist($provider);
        }

        foreach ($data['models'] ?? [] as $m) {
            $model = $this->em->getRepository(Model::class)->findOneBy([
                'providerCode' => $m['provider'],
                'code' => $m['code'],
            ]) ?? new Model();
            $model->setProviderCode($m['provider']);
            $model->setCode($m['code']);
            $this->em->persist($model);
        }

        foreach ($data['prices'] ?? [] as $pr) {
            $validFrom = new \DateTimeImmutable($pr['valid_from']);
            $price = $this->em->getRepository(Price::class)->findOneBy([
                'providerCode' => $pr['provider'],
                'modelCode' => $pr['model'],
                'validFrom' => $validFrom,
            ]) ?? new Price();
            $price->setProviderCode($pr['provider']);
            $price->setModelCode($pr['model']);
            $price->setValidFrom($validFrom);
            $price->setCurrency($pr['currency']);
            $price->setUnit($pr['unit']);
            $rates = $pr['rates'] ?? [];
            $price->setRateInput($rates['input'] ?? null);
            $price->setRateOutput($rates['output'] ?? null);
            $price->setRateCacheRead($rates['cache_read'] ?? null);
            $price->setRateCacheWrite($rates['cache_write'] ?? null);
            $price->setRateAny($rates['any'] ?? null);
            $this->em->persist($price);
        }

        $this->em->flush();
        return Command::SUCCESS;
    }
}
