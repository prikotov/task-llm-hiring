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

#[AsCommand(name: 'app:prices:import', description: 'Import prices JSON')]
class PricesImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em) { parent::__construct(); }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to prices JSON');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_file($file)) {
            $output->getErrorOutput()->writeln("File not found");
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
            $output->getErrorOutput()->writeln("Invalid JSON");
            return Command::FAILURE;
        }

        $providers = $data['providers'] ?? [];
        foreach ($providers as $p) {
            $repo = $this->em->getRepository(Provider::class);
            $provider = $repo->findOneBy(['code' => $p['code']]);
            if (!$provider) {
                $provider = new Provider();
                $provider->setCode($p['code']);
            }
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
            $this->em->persist($provider);
        }

        $models = $data['models'] ?? [];
        foreach ($models as $m) {
            $providerCode = $m['provider'];
            $modelCode = $m['code'];
            $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $providerCode]);
            if (!$provider) {
                continue; // skip?
            }
            $repo = $this->em->getRepository(Model::class);
            $model = $repo->findOneBy(['providerCode' => $providerCode, 'code' => $modelCode]);
            if (!$model) {
                $model = new Model();
                $model->setProviderCode($providerCode);
                $model->setCode($modelCode);
                $this->em->persist($model);
            }
        }

        $prices = $data['prices'] ?? [];
        foreach ($prices as $pr) {
            $providerCode = $pr['provider'];
            $modelCode = $pr['model'];
            $validFrom = new \DateTimeImmutable($pr['valid_from']);
            $repo = $this->em->getRepository(Price::class);
            $price = $repo->findOneBy([
                'providerCode' => $providerCode,
                'modelCode' => $modelCode,
                'validFrom' => $validFrom,
            ]);
            if (!$price) {
                $price = new Price();
                $price->setProviderCode($providerCode);
                $price->setModelCode($modelCode);
                $price->setValidFrom($validFrom);
            }
            $price->setCurrency($pr['currency'] ?? 'USD');
            $price->setUnit($pr['unit'] ?? '');
            $price->setRates($pr['rates'] ?? []);
            $this->em->persist($price);
        }

        $this->em->flush();
        return Command::SUCCESS;
    }
}
