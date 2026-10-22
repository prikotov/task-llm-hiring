<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Model\Model;
use App\Domain\Model\PriceLine;
use App\Domain\Provider\Provider;
use App\Import\ImportException;
use App\Import\PriceCatalogParser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class PricesImportCommand extends Command
{
    protected static $defaultName = 'app:prices:import';

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:prices:import')
            ->setDescription('Импорт провайдеров, моделей и строк цен из JSON-файла')
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с историей цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');

        try {
            $catalog = PriceCatalogParser::parseFile($file);
        } catch (ImportException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $providers = $this->importProviders($catalog['providers']);
        $this->em->flush();
        $this->importModels($catalog['models'], $providers);
        $this->em->flush();
        $this->importPrices($catalog['prices'], $providers);

        $this->em->flush();

        $io->success(sprintf(
            'Импортировано: провайдеров %d, моделей %d, строк цен %d',
            count($catalog['providers']),
            count($catalog['models']),
            count($catalog['prices']),
        ));

        return Command::SUCCESS;
    }

    /**
     * @param array<int, array{code:string,name:string,offPeakStart:?string,offPeakEnd:?string,offPeakMultiplier:?string}> $items
     * @return array<string, Provider> map code => entity
     */
    private function importProviders(array $items): array
    {
        $map = [];
        foreach ($items as $item) {
            /** @var Provider|null $provider */
            $provider = $this->em->getRepository(Provider::class)->findOneBy(['code' => $item['code']]);
            if ($provider === null) {
                $provider = new Provider();
                $provider->setCode($item['code']);
                $this->em->persist($provider);
            }
            $provider->setName($item['name']);
            $provider->setOffPeak($item['offPeakStart'], $item['offPeakEnd'], $item['offPeakMultiplier']);
            $map[$item['code']] = $provider;
        }

        return $map;
    }

    /**
     * @param array<int, array{provider:string,code:string}> $items
     * @param array<string, Provider> $providers
     */
    private function importModels(array $items, array $providers): void
    {
        foreach ($items as $item) {
            $provider = $providers[$item['provider']]
                ?? $this->em->getRepository(Provider::class)->findOneBy(['code' => $item['provider']]);
            if ($provider === null) {
                $provider = new Provider();
                $provider->setCode($item['provider']);
                $provider->setName($item['provider']);
                $this->em->persist($provider);
            }

            $model = $this->em->getRepository(Model::class)->findOneBy([
                'provider' => $provider,
                'code' => $item['code'],
            ]);
            if ($model === null) {
                $model = new Model();
                $model->setProvider($provider);
                $model->setCode($item['code']);
                $this->em->persist($model);
            }
        }
    }

    /**
     * @param array<int, array{provider:string,model:string,validFrom:\DateTimeImmutable,currency:string,unit:string,ratesJson:string}> $items
     * @param array<string, Provider> $providers
     */
    private function importPrices(array $items, array $providers): void
    {
        foreach ($items as $item) {
            $provider = $providers[$item['provider']]
                ?? $this->em->getRepository(Provider::class)->findOneBy(['code' => $item['provider']]);
            if ($provider === null) {
                throw new ImportException('Провайдер "'.$item['provider'].'" не найден');
            }
            $model = $this->em->getRepository(Model::class)->findOneBy([
                'provider' => $provider,
                'code' => $item['model'],
            ]);
            if ($model === null) {
                throw new ImportException('Модель "'.$item['provider'].'/'.$item['model'].'" не найдена');
            }

            $line = $this->em->getRepository(PriceLine::class)->findOneBy([
                'provider' => $provider,
                'model' => $model,
                'validFrom' => $item['validFrom'],
            ]);
            if ($line === null) {
                $line = new PriceLine();
                $line->setProvider($provider);
                $line->setModel($model);
                $line->setValidFrom($item['validFrom']);
                $this->em->persist($line);
            }
            $line->setCurrency($item['currency']);
            $line->setUnit($item['unit']);
            $line->setRatesJson($item['ratesJson']);
        }
    }
}
