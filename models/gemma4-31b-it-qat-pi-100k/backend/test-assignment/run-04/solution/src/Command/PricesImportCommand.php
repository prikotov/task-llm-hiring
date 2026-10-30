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
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Prices JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        $data = json_decode(file_get_contents($file), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return Command::FAILURE;
        }

        foreach ($data['providers'] as $pData) {
            $provider = $this->em->getRepository(Provider::class)->find($pData['code']);
            if (!$provider) {
                $provider = new Provider();
                $provider->code = $pData['code'];
                $this->em->persist($provider);
            }
            $provider->name = $pData['name'];
            $provider->offPeak = $pData['off_peak'];
        }

        foreach ($data['models'] as $mData) {
            $provider = $this->em->getRepository(Provider::class)->find($mData['provider']);
            if (!$provider) {
                // Models should belong to provider, provider should be imported first.
                // But let's be safe.
                return Command::FAILURE;
            }

            $model = $this->em->getRepository(Model::class)->findOneBy([
                'provider' => $provider,
                'code' => $mData['code']
            ]);
            if (!$model) {
                $model = new Model();
                $model->provider = $provider;
                $model->code = $mData['code'];
                $this->em->persist($model);
            }
        }

        foreach ($data['prices'] as $prData) {
            $provider = $this->em->getRepository(Provider::class)->find($prData['provider']);
            $model = $this->em->getRepository(Model::class)->findOneBy([
                'provider' => $provider,
                'code' => $prData['model']
            ]);
            
            $validFrom = new \DateTimeImmutable($prData['valid_from']);
            
            if (!$model) {
                // This should not happen based on JSON structure (models are defined)
                // but let's ignore prices for nonexistent models.
                continue;
            }

            $price = $this->em->getRepository(Price::class)->findOneBy([
                'model' => $model,
                'validFrom' => $validFrom
            ]);

            if (!$price) {
                $price = new Price();
                $price->model = $model;
                $price->validFrom = $validFrom;
                $this->em->persist($price);
            }
            $price->currency = $prData['currency'];
            $price->unit = $prData['unit'];
            $price->rates = $prData['rates'];
        }

        $this->em->flush();
        return Command::SUCCESS;
    }
}
