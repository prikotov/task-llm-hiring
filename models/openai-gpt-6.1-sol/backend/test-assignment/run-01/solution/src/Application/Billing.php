<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Tariff;
use App\Entity\{Model, Price, Provider, Usage};
use Doctrine\ORM\EntityManagerInterface;

final class Billing
{
    public function __construct(private EntityManagerInterface $em, private Tariff $tariff)
    {
    }

    /** Одна блокировка для всех пишущих команд: исключает гонки upsert и двойную тарификацию. */
    private function transaction(callable $work): mixed
    {
        return $this->em->wrapInTransaction(function () use ($work) {
            $this->em->getConnection()->executeQuery('SELECT pg_advisory_xact_lock(71320941)');

            return $work();
        });
    }

    public function importPrices(string $path): void
    {
        $data = Input::file($path);
        $this->transaction(function () use ($data): void {
            foreach (['providers', 'models', 'prices'] as $section) {
                if (!isset($data[$section]) || !is_array($data[$section]) || !array_is_list($data[$section])) {
                    throw new \InvalidArgumentException('Ожидается массив: ' . $section);
                }
            }
            foreach ($data['providers'] as $row) {
                $row = Input::object($row);
                $code = Input::text($row, 'code');
                $provider = $this->em->find(Provider::class, $code) ?? new Provider();
                $provider->code = $code;
                $provider->name = Input::text($row, 'name', null);
                $provider->offPeak = Input::offPeak($row['off_peak'] ?? null);
                $this->em->persist($provider);
                $this->em->flush();
            }
            foreach ($data['models'] as $row) {
                $row = Input::object($row);
                $provider = $this->em->find(Provider::class, Input::text($row, 'provider'));
                if ($provider === null) {
                    throw new \InvalidArgumentException('Неизвестный провайдер модели.');
                }
                $code = Input::text($row, 'code');
                $model = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $code]) ?? new Model();
                $model->provider = $provider;
                $model->code = $code;
                $this->em->persist($model);
                $this->em->flush();
            }
            foreach ($data['prices'] as $row) {
                $row = Input::object($row);
                $provider = $this->em->find(Provider::class, Input::text($row, 'provider'));
                $model = $provider === null ? null : $this->em->getRepository(Model::class)->findOneBy([
                    'provider' => $provider, 'code' => Input::text($row, 'model'),
                ]);
                if ($model === null) {
                    throw new \InvalidArgumentException('Неизвестная модель цены.');
                }
                $date = Input::timestamp($row['valid_from'] ?? null);
                $price = $this->em->getRepository(Price::class)->findOneBy(['model' => $model, 'validFrom' => $date]) ?? new Price();
                $price->model = $model;
                $price->validFrom = $date;
                $price->rates = Input::rates($row);
                $price->unit = $row['unit'];
                $this->em->persist($price);
                $this->em->flush();
            }
        });
    }

    public function importUsage(string $path): void
    {
        $file = @fopen($path, 'rb');
        if ($file === false) {
            throw new \InvalidArgumentException('Не удалось открыть файл: ' . $path);
        }
        try {
            $this->transaction(function () use ($file): void {
                $lineNumber = 0;
                while (($line = fgets($file)) !== false) {
                    ++$lineNumber;
                    try {
                        $row = Input::json($line);
                        $provider = Input::text($row, 'provider');
                        $request = Input::text($row, 'provider_request_id');
                        if ($this->em->getRepository(Usage::class)->findOneBy(['provider' => $provider, 'requestId' => $request]) !== null) {
                            continue;
                        }
                        $usage = new Usage();
                        $usage->provider = $provider;
                        $usage->requestId = $request;
                        $usage->model = Input::text($row, 'model');
                        $usage->userId = Input::text($row, 'user', null);
                        $usage->occurredAt = Input::timestamp($row['ts'] ?? null);
                        $usage->tokens = [];
                        foreach (Tariff::KINDS as $kind) {
                            $value = $row['tokens_' . $kind] ?? null;
                            if (!is_int($value) || $value < 0) {
                                throw new \InvalidArgumentException('Токены должны быть целыми >= 0: ' . $kind);
                            }
                            $usage->tokens[$kind] = $value;
                        }
                        $this->em->persist($usage);
                        $this->em->flush();
                        $this->em->clear();
                    } catch (\Throwable $e) {
                        throw new \InvalidArgumentException('Строка ' . $lineNumber . ': ' . $e->getMessage(), 0, $e);
                    }
                }
                if (!feof($file)) {
                    throw new \RuntimeException('Ошибка чтения JSONL.');
                }
            });
        } finally {
            fclose($file);
        }
    }

    /** @return array{rated: int, unrated: list<string>} */
    public function rate(): array
    {
        return $this->transaction(function (): array {
            $rated = 0;
            $unrated = [];
            $query = $this->em->createQuery('SELECT u FROM App\\Entity\\Usage u WHERE u.cost IS NULL ORDER BY u.id');
            foreach ($query->toIterable() as $usage) {
                $price = $this->em->createQuery(
                    'SELECT p FROM App\\Entity\\Price p JOIN p.model m JOIN m.provider v '
                    . 'WHERE v.code = :provider AND m.code = :model AND p.validFrom <= :ts ORDER BY p.validFrom DESC'
                )->setParameters(['provider' => $usage->provider, 'model' => $usage->model, 'ts' => $usage->occurredAt])
                    ->setMaxResults(1)->getOneOrNullResult();
                if ($price === null) {
                    $unrated[] = sprintf('UNRATED %s/%s %s no_price', $usage->provider, $usage->requestId, str_replace('.000000Z', 'Z', $usage->occurredAt));
                } else {
                    $usage->cost = $this->tariff->calculate($usage->tokens, $price->unit, $price->rates, $price->model->provider->offPeak, $usage->occurredAt);
                    $usage->price = $price;
                    ++$rated;
                    $this->em->flush();
                }
                $this->em->clear();
            }

            return ['rated' => $rated, 'unrated' => $unrated];
        });
    }

    /** Суммирование BCMath не допускает переполнения PHP int для токенов. */
    public function report(?string $from, ?string $to): array
    {
        $qb = $this->em->createQueryBuilder()->select('u')->from(Usage::class, 'u')->where('u.cost IS NOT NULL');
        if ($from !== null) {
            $qb->andWhere('u.occurredAt >= :from')->setParameter('from', Input::timestamp($from));
        }
        if ($to !== null) {
            $qb->andWhere('u.occurredAt < :to')->setParameter('to', Input::timestamp($to));
        }
        $groups = [];
        $total = ['TOTAL', '', '0', '0', '0', '0', '0.000000'];
        foreach ($qb->getQuery()->toIterable() as $usage) {
            $model = $usage->provider . '/' . $usage->model;
            $key = json_encode([$usage->userId, $model], JSON_THROW_ON_ERROR);
            $groups[$key] ??= [$usage->userId, $model, '0', '0', '0', '0', '0.000000'];
            foreach (Tariff::KINDS as $index => $kind) {
                $groups[$key][$index + 2] = bcadd($groups[$key][$index + 2], (string) $usage->tokens[$kind], 0);
                $total[$index + 2] = bcadd($total[$index + 2], (string) $usage->tokens[$kind], 0);
            }
            $groups[$key][6] = bcadd($groups[$key][6], $usage->cost, 6);
            $total[6] = bcadd($total[6], $usage->cost, 6);
            $this->em->detach($usage);
        }
        $rows = array_values($groups);
        usort($rows, static fn (array $a, array $b): int => strcmp($a[0], $b[0]) ?: strcmp($a[1], $b[1]));
        $rows[] = $total;

        return $rows;
    }
}
