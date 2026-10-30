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
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to prices JSON');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_readable($file)) {
            $output->writeError("File not readable");
            return Command::FAILURE;
        }
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $output->writeError("Invalid JSON");
            return Command::FAILURE;
        }

        $providers = $data['providers'] ?? [];
        foreach ($providers as $p) {
            $code = $p['code'];
            $provider = $this->em->find(Provider::class, $code);
            if (!$provider) {
                $provider = new Provider($code, $p['name'] ?? '');
                $this->em->persist($provider);
            } else {
                $provider->setName($p['name'] ?? $provider->getName());
            }
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
            try {
                $validFrom = new \DateTimeImmutable($validFromStr);
            } catch (\Throwable) {
                continue;
            }
            $repo = $this->em->getRepository(Price::class);
            $existing = $repo->findOneBy([
                'providerCode' => $providerCode,
                'modelCode' => $modelCode,
                'validFrom' => $validFrom,
            ]);
            if (!$existing) {
                $price = new Price(
                    $providerCode,
                    $modelCode,
                    $validFrom,
                    $pr['currency'] ?? 'USD',
                    $pr['unit'] ?? '',
                    $pr['rates'] ?? []
                );
                $this->em->persist($price);
            }
        }

        $this->em->flush();
        return Command::SUCCESS;
    }
}
