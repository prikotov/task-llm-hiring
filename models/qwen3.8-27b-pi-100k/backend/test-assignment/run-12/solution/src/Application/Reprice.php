<?php

declare(strict_types=1);

namespace App\Application;

use App\Entity\Rating;
use App\Entity\UsageEventRow;
use App\Infra\Input\FileReader;
use App\Infra\Input\PriceSheetParser;
use App\Repository\ModelRepository;
use App\Repository\PriceRowRepository;
use App\Repository\ProviderRepository;
use App\ValueObject\Money;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * `app:billing:reprice` (бонус).
 *
 * Применяет исправленные строки (upsert по (provider, model, valid_from)),
 * затем пересчитывает **все** rated-события (по свежим правилам).
 */
final class Reprice
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ModelRepository $models,
        private readonly PriceRowRepository $prices,
        private readonly ProviderRepository $providers,
    ) {
    }

    /** @return array{n:int, delta:string} */
    public function run(string $file, OutputInterface $output): array
    {
        $data = FileReader::readJson($file);
        $sheets = PriceSheetParser::parse($data['prices'] ?? []);

        foreach ($sheets as $s) {
            if ($this->providers->find($s->provider) === null) {
                throw new \RuntimeException(sprintf("unknown provider '%s' (must exist in DB)", $s->provider));
            }
        }

        foreach ($sheets as $s) {
            $this->prices->upsert($s->provider, $s->model, $s->validFrom, $s->unit, $s->rates);
        }
        $this->em->flush();

        $rater = new Rater($this->providers->allAsArray());
        $revised = 0;
        $delta = '0';

        $events = $this->em->getRepository(UsageEventRow::class)->findAll();
        foreach ($events as $e) {
            $rating = $this->em->getRepository(Rating::class)
                ->findOneBy(['eventId' => (int) $e->id]);
            if ($rating === null) {
                continue;
            }

            $oldCost = $rating->cost;
            $bestRow = $this->em->clear();
            $e = $this->em->getRepository(UsageEventRow::class)->find((int) $e->id);

            if ($e === null) {
                continue;
            }

            $best = $this->prices->findLatestUpTo($e->provider, $e->model, $e->ts);
            if ($best === null) {
                continue;
            }

            $newCost = $rater->costRounded($e, $best);
            $d = bcsub($newCost, $oldCost, 40);
            $delta = bcadd($delta, $d, 40);

            $rating->cost = Money::of($newCost)->value();
            $rating->priceRowId = (int) $best->id;
            $revised++;
        }

        $this->em->flush();

        return ['n' => $revised, 'delta' => $delta];
    }
}
