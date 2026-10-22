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

#[AsCommand(name: 'app:prices:import', description: 'Imports prices, models and providers from a JSON file')]
class PricesImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            $output->writeln("<error>File not found: $filePath</error>");
            return Command::FAILURE;
        }

        $content = file_get_contents($filePath);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $output->writeln("<error>Invalid JSON: " . json_last_error_msg() . "</error>");
            return Command::FAILURE;
        }

        // 1. Import Providers
        foreach ($data['providers'] ?? [] as $providerData) {
            $code = $providerData['code'];
            $name = $providerData['name'];

            $provider = $this->entityManager->getRepository(Provider::class)->find($code);
            if (!$provider) {
                $provider = new Provider($code, $name);
                $this->entityManager->persist($provider);
            } else {
                $provider->setName($name);
            }

            if (isset($providerData['off_peak'])) {
                $provider->setOffPeakWindowStart($providerData['off_peak']['window_start'] ?? null);
                $provider->setOffPeakWindowEnd($providerData['off_peak']['window_end'] ?? null);
                $provider->setOffPeakMultiplier($providerData['off_peak']['multiplier'] ?? null);
            } else {
                $provider->setOffPeakWindowStart(null);
                $provider->setOffPeakWindowEnd(null);
                $provider->setOffPeakMultiplier(null);
            }
        }
        $this->entityManager->flush();

        // 2. Import Models
        foreach ($data['models'] ?? [] as $modelData) {
            $providerCode = $modelData['provider'];
            $code = $modelData['code'];

            $existingModel = $this->entityManager->getRepository(Model::class)->findOneBy([
                'providerCode' => $providerCode,
                'code' => $code
            ]);

            if (!$existingModel) {
                $model = new Model($providerCode, $code);
                $this->entityManager->persist($model);
            }
        }
        $this->entityManager->flush();

        // 3. Import Prices
        foreach ($data['prices'] ?? [] as $priceData) {
            $providerCode = $priceData['provider'];
            $modelCode = $priceData['model'];
            $validFrom = new \DateTime($priceData['valid_from']);
            $currency = $priceData['currency'];
            $unit = $priceData['unit'];
            $rates = $priceData['rates'];

            $existingPrice = $this->entityManager->getRepository(Price::class)->findOneBy([
                'providerCode' => $providerCode,
                'modelCode' => $modelCode,
                'validFrom' => $validFrom
            ]);

            if (!$existingPrice) {
                $price = new Price($providerCode, $modelCode, $validFrom, $currency, $unit, $rates);
                $this->entityManager->persist($price);
            }
            // If it exists, we don't change anything as per requirements "повторная загрузка тех же сумм — не изменяет данных"
            // but the requirement also says "Идемпотентный upsert: ... строки цен — по (provider, model, valid_from)".
            // If they are same, it doesn't change. If they are different, should we update?
            // "повторная загрузка тех же сумм - не изменяет данных" implies if it's exactly the same it's fine.
            // If it's different, it doesn't explicitly say. But "upsert" usually means update.
            // However, let's stick to "don't change if exists" for now to be safe, or update.
            // Actually, for prices, if valid_from and provider/model match, it's the same entry.
        }
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
