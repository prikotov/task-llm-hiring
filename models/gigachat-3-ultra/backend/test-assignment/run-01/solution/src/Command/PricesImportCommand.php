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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Импорт справочника цен провайдеров и моделей'
)]
final class PricesImportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с ценами');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_file($file)) {
            $output->writeln('<error>Файл не найден: ' . $file . '</error>');
            return Command::FAILURE;
        }

        $json = file_get_contents($file);
        if ($json === false) {
            $output->writeln('<error>Не удалось прочитать файл: ' . $file . '</error>');
            return Command::FAILURE;
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $output->writeln('<error>Некорректный JSON: ' . json_last_error_msg() . '</error>');
            return Command::FAILURE;
        }

        $serializer = new Serializer([new ArrayDenormalizer(), new ObjectNormalizer()], [new JsonEncoder()]);

        $this->em->beginTransaction();
        try {
            // Upsert providers
            foreach ($data['providers'] as $pData) {
                $provider = $this->em->getRepository(Provider::class)->find($pData['code']);
                if (!$provider) {
                    $provider = new Provider($pData['code'], $pData['name']);
                    $this->em->persist($provider);
                } else {
                    $provider->setName($pData['name']);
                }
                if ($pData['off_peak'] !== null) {
                    $provider->setOffPeakWindowStart($pData['off_peak']['window_start']);
                    $provider->setOffPeakWindowEnd($pData['off_peak']['window_end']);
                    $provider->setOffPeakMultiplier($pData['off_peak']['multiplier']);
                } else {
                    $provider->setOffPeakWindowStart(null);
                    $provider->setOffPeakWindowEnd(null);
                    $provider->setOffPeakMultiplier(null);
                }
            }

            // Upsert models
            foreach ($data['models'] as $mData) {
                $provider = $this->em->getRepository(Provider::class)->find($mData['provider']);
                if (!$provider) {
                    $output->writeln('<error>Провайдер не найден: ' . $mData['provider'] . '</error>');
                    $this->em->rollback();
                    return Command::FAILURE;
                }
                $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $mData['code']]);
                if (!$model) {
                    $model = new Model($provider, $mData['code']);
                    $this->em->persist($model);
                }
            }

            // Upsert prices
            foreach ($data['prices'] as $prData) {
                $provider = $this->em->getRepository(Provider::class)->find($prData['provider']);
                $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $prData['model']]);
                if (!$model) {
                    $output->writeln('<error>Модель не найдена: ' . $prData['provider'] . '/' . $prData['model'] . '</error>');
                    $this->em->rollback();
                    return Command::FAILURE;
                }
                $validFrom = new \DateTimeImmutable($prData['valid_from']);
                $existing = $this->em->getRepository(Price::class)->findOneBy([
                    'provider' => $provider,
                    'model' => $model,
                    'validFrom' => $validFrom,
                ]);
                if ($existing) {
                    continue; // идемпотентность
                }
                $price = new Price(
                    $provider,
                    $model,
                    $validFrom,
                    $prData['currency'],
                    $prData['unit'],
                    $prData['rates']['input'],
                    $prData['rates']['output'],
                    $prData['rates']['cache_read'],
                    $prData['rates']['cache_write'],
                    $prData['unit'] === 'per_1m_tokens_unified' ? $prData['rates']['any'] : null
                );
                $this->em->persist($price);
            }

            $this->em->flush();
            $this->em->commit();
            $output->writeln('<info>Цены успешно импортированы</info>');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->em->rollback();
            $output->writeln('<error>Ошибка импорта: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
