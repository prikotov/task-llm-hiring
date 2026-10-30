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

#[AsCommand(name: 'app:prices:import', description: 'Import prices JSON file')]
class PricesImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em) { parent::__construct(); }

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
            $output->getErrorOutput()->writeln('Cannot read file');
            return Command::FAILURE;
        }
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $output->getErrorOutput()->writeln('Invalid JSON');
            return Command::FAILURE;
        }

        $providerRepo = $this->em->getRepository(Provider::class);
        foreach ($data['providers'] ?? [] as $p) {
            $code = $p['code'];
            $entity = $providerRepo->findOneBy(['code' => $code]) ?? new Provider();
            $entity->setCode($code);
            $entity->setName($p['name'] ?? '');
            $offPeak = $p['off_peak'] ?? null;
            if (is_array($offPeak)) {
                $entity->setOffPeakStart($offPeak['window_start'] ?? null);
                $entity->setOffPeakEnd($offPeak['window_end'] ?? null);
                $entity->setOffPeakMultiplier((string)($offPeak['multiplier'] ?? '1'));
            } else {
                $entity->setOffPeakStart(null);
                $entity->setOffPeakEnd(null);
                $entity->setOffPeakMultiplier(null);
            }
            $this->em->persist($entity);
        }

        $modelRepo = $this->em->getRepository(Model::class);
        foreach ($data['models'] ?? [] as $m) {
            $provider = $m['provider'];
            $code = $m['code'];
            $entity = $modelRepo->findOneBy(['providerCode' => $provider, 'code' => $code]) ?? new Model();
            $entity->setProviderCode($provider);
            $entity->setCode($code);
            $this->em->persist($entity);
        }

        $priceRepo = $this->em->getRepository(Price::class);
        foreach ($data['prices'] ?? [] as $p) {
            $provider = $p['provider'];
            $model = $p['model'];
            $validFrom = new \DateTimeImmutable($p['valid_from']);
            $entity = $priceRepo->findOneBy([
                'providerCode' => $provider,
                'modelCode' => $model,
                'validFrom' => $validFrom,
            ]) ?? new Price();
            $entity->setProviderCode($provider);
            $entity->setModelCode($model);
            $entity->setValidFrom($validFrom);
            $entity->setCurrency($p['currency'] ?? 'USD');
            $entity->setUnit($p['unit'] ?? '');
            $rates = $p['rates'] ?? [];
            $entity->setInputRate(isset($rates['input']) ? (string)$rates['input'] : null);
            $entity->setOutputRate(isset($rates['output']) ? (string)$rates['output'] : null);
            $entity->setCacheReadRate(isset($rates['cache_read']) ? (string)$rates['cache_read'] : null);
            $entity->setCacheWriteRate(isset($rates['cache_write']) ? (string)$rates['cache_write'] : null);
            $entity->setAnyRate(isset($rates['any']) ? (string)$rates['any'] : null);
            $this->em->persist($entity);
        }

        $this->em->flush();

        return Command::SUCCESS;
    }
}
