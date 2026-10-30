<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Money\DecimalMoney;
use App\Domain\Pricing\OffPeakWindow;
use App\Domain\Usage\TokenUsage;
use App\Infrastructure\Doctrine\ModelEntity;
use App\Infrastructure\Doctrine\ProviderEntity;
use App\Infrastructure\Doctrine\PriceRowEntity;
use App\Infrastructure\Doctrine\UsageEventEntity;
use Doctrine\ORM\EntityManagerInterface;

final class ApplicationServices
{
    public const TOKENS_PER_UNIT = 1_000_000;

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    // ── pricing import ────────────────────────────────────────────────────────

    /**
     * @param array $payload decoded JSON (must have "providers", "models", "prices" keys)
     * @throws \RuntimeException on invalid payload shape
     */
    public function importPrices(array $payload): ImportResult
    {
        $errors = [];
        $okProviders = 0;
        $okModels = 0;
        $okPrices = 0;
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $utc = new \DateTimeZone('UTC');

        // ── providers ──
        if (!isset($payload['providers']) || !is_array($payload['providers'])) {
            throw new \RuntimeException('"providers" top-level key required (array)');
        }
        foreach ($payload['providers'] as $idx => $p) {
            $label = sprintf('providers[%d]', $idx);
            try {
                $row = self::parseProvider($p, $label);
            } catch (\InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
                continue;
            }
            $existing = $this->em->getRepository(ProviderEntity::class)->findOneBy(['code' => $row['code']]);
            $entity = $existing ?? new ProviderEntity();
            $entity->code = $row['code'];
            $entity->name = $row['name'];
            $entity->offPeakStartMin   = $row['offPeak']['startMin']   ?? null;
            $entity->offPeakEndMin     = $row['offPeak']['endMin']     ?? null;
            $entity->offPeakMultiplier = $row['offPeak']['multiplier'] ?? null;
            if ($existing === null) $entity->createdAt = $now;
            $this->em->persist($entity);
            $okProviders++;
        }

        // ── models ──
        if (!isset($payload['models']) || !is_array($payload['models'])) {
            throw new \RuntimeException('"models" top-level key required (array)');
        }
        $providerCodes = array_filter(array_map(
            fn($p) => is_array($p) && isset($p['code']) ? $p['code'] : null,
            $payload['providers']));
        foreach ($payload['models'] as $idx => $m) {
            $label = sprintf('models[%d]', $idx);
            try {
                $row = self::parseModel($m, $label);
            } catch (\InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
                continue;
            }
            if (!in_array($row['providerCode'], $providerCodes, true)) {
                $errors[] = "$label: provider {$row['providerCode']} not declared in providers section";
                continue;
            }
            $existing = $this->em->getRepository(ModelEntity::class)->findOneBy([
                'providerCode' => $row['providerCode'], 'code' => $row['code'],
            ]);
            $entity = $existing ?? new ModelEntity();
            $entity->providerCode = $row['providerCode'];
            $entity->code         = $row['code'];
            $entity->modelName    = $row['modelName'];
            if ($existing === null) $entity->createdAt = $now;
            $this->em->persist($entity);
            $okModels++;
        }

        // ── price rows ──
        $priceRows = $payload['prices'] ?? $payload['price_rows'] ?? [];
        if ($priceRows !== [] && !is_array($priceRows)) {
            throw new \RuntimeException('"prices" must be an array');
        }
        foreach ($priceRows as $idx => $pr) {
            $label = sprintf('prices[%d]', $idx);
            try {
                $r = self::parsePriceRow($pr, $label);
            } catch (\InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
                continue;
            }
            $validFrom = new \DateTimeImmutable($r['validFrom']);
            $existing = $this->em->createQuery(
                'SELECT x FROM App\Infrastructure\Doctrine\PriceRowEntity x
                 WHERE x.providerCode = :pc AND x.modelCode = :mc AND x.validFrom = :ts'
            )->setParameter('pc', $r['provider'])->setParameter('mc', $r['model'])->setParameter('ts', $validFrom)
              ->getOneOrNullResult();
            $entity = $existing ?? new PriceRowEntity();
            $entity->providerCode = $r['provider'];
            $entity->modelCode    = $r['model'];
            $entity->validFrom    = $validFrom;
            $entity->currency     = $r['currency'];
            $entity->unit         = $r['unit'];
            $entity->ratesJson    = json_encode($r['rates'], JSON_THROW_ON_ERROR);
            if ($existing === null) $entity->createdAt = $now;
            $this->em->persist($entity);
            $okPrices++;
        }

        $this->em->flush();
        return new ImportResult(
            importedCount: $okProviders + $okModels + $okPrices,
            providers:     $okProviders,
            models:        $okModels,
            prices:        $okPrices,
            usageEvents:   0,
            errors:        $errors,
        );
    }

    private static function parseProvider(array $p, string $label): array
    {
        if (!is_array($p)) throw new \InvalidArgumentException("$label: must be an object");
        $code = $p['code'] ?? null;
        if (!is_string($code) || $code === '') throw new \InvalidArgumentException("$label: \"code\" required");
        $name = $p['name'] ?? $code;
        if (!is_string($name) || $name === '') throw new \InvalidArgumentException("$label: \"name\" must be non-empty string");

        $offPeak = null;
        if (isset($p['off_peak']) && $p['off_peak'] !== null) {
            $op = $p['off_peak'];
            if (!is_array($op)) throw new \InvalidArgumentException("$label: off_peak must be an object");
            $startRaw = $op['window_start'] ?? null;
            $endRaw   = $op['window_end']   ?? null;
            $multi    = $op['multiplier']   ?? null;
            if (!is_string($startRaw) || !preg_match('/^\d{1,2}:\d{2}$/', $startRaw)) throw new \InvalidArgumentException("$label: off_peak.window_start must be HH:MM (string)");
            if (!is_string($endRaw)   || !preg_match('/^\d{1,2}:\d{2}$/', $endRaw))   throw new \InvalidArgumentException("$label: off_peak.window_end must be HH:MM (string)");
            if ($multi === null)                                          throw new \InvalidArgumentException("$label: off_peak.multiplier required");
            if (is_string($multi)) {
                if (!preg_match('/^-?(0|[1-9]\d*)(\.\d{1,6})?$/', $multi)) throw new \InvalidArgumentException("$label: off_peak.multiplier must be decimal string (≤6 dp)");
            } elseif (!is_numeric($multi)) {
                throw new \InvalidArgumentException("$label: off_peak.multiplier must be a string or number");
            } else {
                $multi = (string)$multi;
            }
            try {
                $w = OffPeakWindow::fromHhmm($startRaw, $endRaw, (string)$multi);
                $offPeak = ['startMin' => $w->startMinutes, 'endMin' => $w->endMinutes, 'multiplier' => (string)$multi];
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException("$label: off_peak — {$e->getMessage()}");
            }
        }

        return ['code' => $code, 'name' => $name, 'offPeak' => $offPeak];
    }

    private static function parseModel(array $m, string $label): array
    {
        if (!is_array($m)) throw new \InvalidArgumentException("$label: must be an object");
        $pc = $m['provider'] ?? null;
        $c  = $m['code']     ?? null;
        if (!is_string($pc) || $pc === '') throw new \InvalidArgumentException("$label: \"provider\" required");
        if (!is_string($c)  || $c  === '') throw new \InvalidArgumentException("$label: \"code\" required");
        $mn = $m['model_name'] ?? null;
        if ($mn !== null && !is_string($mn)) throw new \InvalidArgumentException("$label: \"model_name\" must be string");

        return ['providerCode' => $pc, 'code' => $c, 'modelName' => $mn];
    }

    private static function parsePriceRow(array $pr, string $label): array
    {
        if (!is_array($pr)) throw new \InvalidArgumentException("$label: must be an object");
        $pc = $pr['provider'] ?? null;
        $mc = $pr['model']    ?? $pr['model_code'] ?? null;
        $vf = $pr['valid_from'] ?? null;
        $cur = $pr['currency'] ?? null;
        $unit = $pr['unit'] ?? null;
        $rates = $pr['rates'] ?? null;

        if (!is_string($pc)   || $pc   === '') throw new \InvalidArgumentException("$label: \"provider\" required");
        if (!is_string($mc)   || $mc   === '') throw new \InvalidArgumentException("$label: \"model\" required");
        if ($vf === null)                       throw new \InvalidArgumentException("$label: \"valid_from\" required");
        if ($cur !== null && !is_string($cur))  throw new \InvalidArgumentException("$label: \"currency\" must be string");
        if (!is_string($unit) || $unit === '')  throw new \InvalidArgumentException("$label: \"unit\" required");
        if (!is_array($rates) || $rates === []) throw new \InvalidArgumentException("$label: \"rates\" must be non-empty object");

        try {
            new \DateTimeImmutable((string)$vf);
        } catch (\Exception) {
            throw new \InvalidArgumentException("$label: valid_from not a valid ISO-8601 date");
        }

        $unit = (string)$unit;
        if ($unit === 'per_1m_tokens_unified') {
            if (!array_key_exists('any', $rates)) throw new \InvalidArgumentException("$label: unified rate must define \"any\"");
        } elseif ($unit === 'per_1m_tokens') {
            foreach (['input','output','cache_read','cache_write'] as $k) {
                if (!array_key_exists($k, $rates)) throw new \InvalidArgumentException("$label: per_1m_tokens must define rate \"$k\"");
            }
        } else {
            throw new \InvalidArgumentException("$label: unknown unit $unit");
        }

        foreach ($rates as $k => $v) {
            $sv = is_string($v) ? $v : (string)$v;
            if (!preg_match('/^-?(0|[1-9]\d*)(\.\d{1,6})?$/', $sv)) {
                throw new \InvalidArgumentException("$label: rates.$k invalid: $sv");
            }
            $rates[$k] = $sv;
        }

        return [
            'provider' => $pc, 'model' => $mc,
            'validFrom' => (string)$vf,
            'currency' => $cur ?? 'USD',
            'unit' => $unit,
            'rates' => $rates,
        ];
    }

    // ── usage import ───────────────────────────────────────────────────────────

    public function importUsage(string $filePath): ImportResult
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new \RuntimeException(sprintf('not found or not readable: %s', $filePath));
        }
        $fh = fopen($filePath, 'rb');
        if ($fh === false) throw new \RuntimeException('could not open file for reading');

        $errors = [];
        $ok = 0;
        $dups = 0;
        $lineNo = 0;
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $utc = new \DateTimeZone('UTC');
        $seen = []; // pc\x00rid => true (session-level dedup)

        while (($line = fgets($fh)) !== false) {
            $lineNo++;
            $line = trim($line);
            if ($line === '') continue;
            $label = "line $lineNo";

            $obj = json_decode($line, true);
            if (!is_array($obj)) { $errors[] = "$label: not valid JSON"; continue; }

            $pc = $obj['provider'] ?? null;
            $rid = $obj['provider_request_id'] ?? null;
            $tsRaw = $obj['ts'] ?? $obj['ts_utc'] ?? null;
            $mc = $obj['model'] ?? $obj['model_code'] ?? null;
            $user = $obj['user'] ?? null;

            if (!is_string($pc)   || $pc   === '') { $errors[] = "$label: provider required"; continue; }
            if (!is_string($rid)  || $rid  === '') { $errors[] = "$label: provider_request_id required"; continue; }
            if ($tsRaw === null)                   { $errors[] = "$label: ts required"; continue; }
            if (!is_string($mc)   || $mc   === '') { $errors[] = "$label: model required"; continue; }
            if (!is_string($user) || $user === '') { $errors[] = "$label: user required"; continue; }

            try {
                $tsUtc = (new \DateTimeImmutable((string)$tsRaw, $utc));
            } catch (\Exception) { $errors[] = "$label: invalid ts: $tsRaw"; continue; }
            $tsUtc = $tsUtc->setTimezone($utc);

            $key = $pc . "\x00" . $rid;
            if (isset($seen[$key])) { $dups++; continue; }
            $seen[$key] = true;

            // provider + model must exist in DB
            if ($this->em->getRepository(ProviderEntity::class)->findOneBy(['code' => $pc]) === null) {
                $errors[] = "$label: provider $pc not in price book"; continue;
            }
            if ($this->em->getRepository(ModelEntity::class)->findOneBy(['providerCode' => $pc, 'code' => $mc]) === null) {
                $errors[] = "$label: model $mc not found for provider $pc"; continue;
            }

            // token fields — direct keys in the row
            $pickInt = fn(array $o, string $k): int => match(true) {
                is_int($o[$k] ?? null) => $o[$k],
                is_string($o[$k] ?? null) && preg_match('/^\d+$/', $o[$k]) => (int)$o[$k],
                true => 0,
            };
            $tokIn  = $pickInt($obj, 'tokens_input');
            $tokOut = $pickInt($obj, 'tokens_output');
            $tokCR  = $pickInt($obj, 'tokens_cache_read');
            $tokCW  = $pickInt($obj, 'tokens_cache_write');
            if ($tokIn + $tokOut + $tokCR + $tokCW === 0) {
                $errors[] = "$label: all-zero token counts"; continue;
            }

            // DB-level dedup: if already in DB from a previous run, skip
            $exists = $this->em->getRepository(UsageEventEntity::class)->findOneBy([
                'providerCode' => $pc, 'providerRequestId' => $rid,
            ]);
            if ($exists !== null) { $dups++; continue; }

            $entity = new UsageEventEntity();
            $entity->providerCode       = $pc;
            $entity->providerRequestId  = $rid;
            $entity->user               = $user;
            $entity->tsUtc              = $tsUtc;
            $entity->modelCode          = $mc;
            $entity->tokensInput        = $tokIn;
            $entity->tokensOutput       = $tokOut;
            $entity->tokensCacheRead    = $tokCR;
            $entity->tokensCacheWrite   = $tokCW;
            $entity->ratingState        = 'pending';
            $entity->currency           = null;
            $entity->rateSpecUnit       = null;
            $entity->rateSpecJson       = null;
            $entity->exactCost          = null;
            $entity->cost               = null;
            $entity->offPeakApplied     = false;
            $entity->updatedAt          = $now;
            $this->em->persist($entity);
            $ok++;
        }
        fclose($fh);
        $this->em->flush();

        if ($errors !== [] && $ok === 0) {
            // all lines failed → treat as fatal
            throw new \RuntimeException(implode(' ', $errors));
        }

        return new ImportResult(
            importedCount: $ok,
            providers: 0, models: 0, prices: 0,
            usageEvents: $ok,
            errors: $errors,
        );
    }

    // ── reprice (rate) ─────────────────────────────────────────────────────────

    /**
     * Rate all pending + unrated events. Returns RepriceResult.
     */
    public function rate(): RepriceResult
    {
        $em = $this->em;
        $utc = new \DateTimeZone('UTC');
        $now = new \DateTimeImmutable('now', $utc);

        // events to rate: state is 'pending' (new) or 'unrated' (re-try)
        $events = $em->createQuery(
            'SELECT e FROM App\Infrastructure\Doctrine\UsageEventEntity e
             WHERE e.ratingState IN (\'pending\', \'unrated\')
             ORDER BY e.providerCode ASC, e.providerRequestId ASC'
        )->getResult();

        $ok = 0;
        $unrated = 0;
        $stderr = []; // per-event details

        foreach ($events as $u) {
            $rowEntity = $em->createQuery(
                'SELECT r FROM App\Infrastructure\Doctrine\PriceRowEntity r
                 WHERE r.providerCode = :pc AND r.modelCode = :mc AND r.validFrom <= :ts
                 ORDER BY r.validFrom DESC'
            )->setParameter('pc', $u->providerCode)
             ->setParameter('mc', $u->modelCode)
             ->setParameter('ts', $u->tsUtc)
             ->setMaxResults(1)
             ->getOneOrNullResult();

            if ($rowEntity === null) {
                $u->ratingState = 'unrated';
                $u->exactCost = null;
                $u->cost = null;
                $u->currency = null;
                $u->rateSpecUnit = null;
                $u->rateSpecJson = null;
                $u->offPeakApplied = false;
                $u->updatedAt = $now;
                $stderr[] = sprintf('UNRATED %s/%s %s no_price',
                    $u->providerCode, $u->providerRequestId,
                    $u->tsUtc->format(\DateTimeInterface::ATOM));
                $unrated++;
                continue;
            }

            $rateSpec = json_decode($rowEntity->ratesJson, true, 512, JSON_THROW_ON_ERROR);
            $unit     = $rowEntity->unit;
            $tok      = new TokenUsage($u->tokensInput, $u->tokensOutput, $u->tokensCacheRead, $u->tokensCacheWrite);

            $cost = self::exactCost($unit, $rateSpec, $tok);

            // off-peak
            $offAppl = false;
            $provider = $em->getRepository(ProviderEntity::class)->findOneBy(['code' => $u->providerCode]);
            if ($provider !== null
                && $provider->offPeakStartMin !== null
                && $provider->offPeakEndMin   !== null
                && $provider->offPeakMultiplier !== null
            ) {
                $w = new OffPeakWindow(
                    $provider->offPeakStartMin,
                    $provider->offPeakEndMin,
                    $provider->offPeakMultiplier,
                );
                $utcMin = (int)$u->tsUtc->format('G') * 60 + (int)$u->tsUtc->format('i');
                if ($w->inWindow($utcMin)) {
                    $cost = $cost->multiply(DecimalMoney::fromDecimalString($provider->offPeakMultiplier));
                    $offAppl = true;
                }
            }

            $rounded = $cost->roundTo(DecimalMoney::ROUND_SCALE);

            $u->ratingState  = 'rated';
            $u->exactCost    = $cost->toBcString();
            $u->cost         = $rounded->toBcString();
            $u->currency     = $rowEntity->currency;
            $u->rateSpecUnit = $unit;
            $u->rateSpecJson = $rowEntity->ratesJson;
            $u->offPeakApplied = $offAppl;
            $u->updatedAt    = $now;
            $ok++;
        }

        $em->flush();
        return new RepriceResult($ok, $unrated, 0, $stderr);
    }

    // ── report ─────────────────────────────────────────────────────────────────

    /**
     * @param \DateTimeImmutable|null $from inclusive
     * @param \DateTimeImmutable|null $to   exclusive
     * @return array rows + totals
     */
    public function reportRange(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $em = $this->em;
        $where = "e.ratingState = 'rated'";
        $params = [];

        if ($from !== null) { $where .= ' AND e.tsUtc >= :from'; $params['from'] = $from; }
        if ($to   !== null) { $where .= ' AND e.tsUtc <  :to';   $params['to']   = $to; }

        $events = $em->createQuery(
            'SELECT e FROM App\Infrastructure\Doctrine\UsageEventEntity e
             WHERE ' . $where . '
             ORDER BY e.user ASC, e.providerCode ASC, e.modelCode ASC'
        )
          ->setParameters($params)
          ->getResult();

        $groups = []; // "user|provider/model" => [...counts]
        $totals = ['input' => 0, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0, 'cost' => DecimalMoney::zero()];

        foreach ($events as $e) {
            $model = $e->providerCode . '/' . $e->modelCode;
            $key = $e->user . "\x00" . $model;
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $e->user, 'model' => $model,
                    'input' => 0, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0,
                    'cost' => DecimalMoney::zero(),
                ];
            }
            $groups[$key]['input']      += $e->tokensInput;
            $groups[$key]['output']     += $e->tokensOutput;
            $groups[$key]['cache_read'] += $e->tokensCacheRead;
            $groups[$key]['cache_write']+= $e->tokensCacheWrite;
            $groups[$key]['cost']       = $groups[$key]['cost']->add(DecimalMoney::fromDecimalString($e->cost));

            $totals['input']      += $e->tokensInput;
            $totals['output']     += $e->tokensOutput;
            $totals['cache_read'] += $e->tokensCacheRead;
            $totals['cache_write']+= $e->tokensCacheWrite;
            $totals['cost']       = $totals['cost']->add(DecimalMoney::fromDecimalString($e->cost));
        }

        usort($groups, fn($a, $b) => strcasecmp($a['user'] . "\x00" . $a['model'], $b['user'] . "\x00" . $b['model']));

        return ['groups' => array_values($groups), 'totals' => $totals];
    }

    // ── reprice bonus: app:billing:reprice ─────────────────────────────────────

    public function repriceFile(array $payload): RepriceResult
    {
        // 1. Upsert the new price rows (same logic as importPrices "prices" section).
        // 2. Find all rated events whose rateSpec matches a replaced row (same provider, model, valid_from).
        // 3. Re-rate those events; compute delta.

        $em = $this->em;
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $oldCosts = []; // event id => old cost string

        $priceRows = $payload['prices'] ?? [];
        $replaced = []; // list of [provider, model, validFrom]

        foreach ($priceRows as $idx => $pr) {
            $label = sprintf('prices[%d]', $idx);
            try {
                $r = self::parsePriceRow($pr, $label);
            } catch (\InvalidArgumentException) {
                // skip bad rows in a reprice file (only the good ones replace)
                continue;
            }
            $validFrom = new \DateTimeImmutable($r['validFrom']);

            // verify provider+model exist
            if ($em->getRepository(ProviderEntity::class)->findOneBy(['code' => $r['provider']]) === null) continue;
            if ($em->getRepository(ModelEntity::class)->findOneBy(['providerCode' => $r['provider'], 'code' => $r['model']]) === null) continue;

            $existing = $em->createQuery(
                'SELECT x FROM App\Infrastructure\Doctrine\PriceRowEntity x
                 WHERE x.providerCode = :pc AND x.modelCode = :mc AND x.validFrom = :ts'
            )->setParameter('pc', $r['provider'])->setParameter('mc', $r['model'])->setParameter('ts', $validFrom)
              ->getOneOrNullResult();

            $entity = $existing ?? new PriceRowEntity();
            $entity->providerCode = $r['provider'];
            $entity->modelCode    = $r['model'];
            $entity->validFrom    = $validFrom;
            $entity->currency     = $r['currency'];
            $entity->unit         = $r['unit'];
            $entity->ratesJson    = json_encode($r['rates'], JSON_THROW_ON_ERROR);
            if ($existing === null) $entity->createdAt = $now;
            $em->persist($entity);
            $replaced[] = [$r['provider'], $r['model'], $validFrom];
        }
        $em->flush();

        // Find events that were rated using one of the replaced rows
        // An event was rated with row X if: X.providerCode = event.providerCode
        // AND X.modelCode = event.modelCode AND X.validFrom <= event.tsUtc
        // AND X is the most recent row at event.tsUtc (i.e. no other row with validFrom in (X.validFrom, event.tsUtc])
        // Simplified: for each replaced row, find events whose rateSpecJson matches that row's (provider, model, valid_from).

        $recounted = 0;
        $delta = DecimalMoney::zero();

        foreach ($replaced as [$provider, $model, $validFrom]) {
            // find events that were using this exact row (identified by rateSpecJson containing provider+model)
            // We can't easily query by JSON content, so we fetch all rated events for this provider+model
            // and re-evaluate which were actually using this row.

            $events = $em->createQuery(
                'SELECT e FROM App\Infrastructure\Doctrine\UsageEventEntity e
                 WHERE e.ratingState = \'rated\'
                   AND e.providerCode = :pc AND e.modelCode = :mc
                 ORDER BY e.tsUtc ASC'
            )->setParameter('pc', $provider)->setParameter('mc', $model)->getResult();

            foreach ($events as $e) {
                // Determine if this event was rated under the replaced row.
                // The event's rateSpecJson should match this row — but after upsert, the row is new.
                // We need to check if the OLD row (before replacement) was the in-force price at e.tsUtc.
                // Practically: re-rate using the NEW book, but only if the event's old row was the replaced one.
                // For this implementation we re-rate ALL events for this provider/model that have tsUtc >= validFrom
                // AND whose "best price at ts" was the replaced row.

                // Simple heuristic: re-rate if e.tsUtc >= validFrom AND the event's old cost doesn't already match
                // the new row. We can just re-rate and compute delta vs stored cost.
                $newRow = $em->createQueryBuilder()
                    ->select('r')->from(PriceRowEntity::class, 'r')
                    ->where('r.providerCode = :pc')->andWhere('r.modelCode = :mc')
                    ->andWhere('r.validFrom <= :ts')->orderBy('r.validFrom', 'DESC')
                    ->setParameter('pc', $provider)->setParameter('mc', $model)->setParameter('ts', $e->tsUtc)
                    ->setMaxResults(1)->getQuery()->getOneOrNullResult();

                if ($newRow === null) continue;
                // Only re-rate if this was the row that was replaced (validFrom matches)
                if ($newRow->validFrom->format('Y-m-d\TH:i:s') !== $validFrom->format('Y-m-d\TH:i:s')) continue;

                $oldCost = $e->cost !== null ? DecimalMoney::fromDecimalString($e->cost) : DecimalMoney::zero();
                $tok = new TokenUsage($e->tokensInput, $e->tokensOutput, $e->tokensCacheRead, $e->tokensCacheWrite);
                $newCost = self::exactCost($newRow->unit, json_decode($newRow->ratesJson, true), $tok);

                // apply off-peak
                $offAppl = false;
                $prov = $em->getRepository(ProviderEntity::class)->findOneBy(['code' => $provider]);
                if ($prov !== null && $prov->offPeakStartMin !== null && $prov->offPeakEndMin !== null && $prov->offPeakMultiplier !== null) {
                    $w = new OffPeakWindow($prov->offPeakStartMin, $prov->offPeakEndMin, $prov->offPeakMultiplier);
                    if ($w->inWindow((int)$e->tsUtc->format('G') * 60 + (int)$e->tsUtc->format('i'))) {
                        $newCost = $newCost->multiply(DecimalMoney::fromDecimalString($prov->offPeakMultiplier));
                        $offAppl = true;
                    }
                }
                $newRounded = $newCost->roundTo(DecimalMoney::ROUND_SCALE);
                $delta = $delta->add($newRounded->subtract($oldCost));

                $e->exactCost      = $newCost->toBcString();
                $e->cost           = $newRounded->toBcString();
                $e->rateSpecUnit   = $newRow->unit;
                $e->rateSpecJson   = $newRow->ratesJson;
                $e->offPeakApplied = $offAppl;
                $e->updatedAt      = $now;
                $recounted++;
            }
        }
        $em->flush();

        return new RepriceResult($recounted, 0, 0, []);
    }

    // ── static helpers ─────────────────────────────────────────────────────────

    private static function exactCost(string $unit, array $rateSpec, TokenUsage $t): DecimalMoney
    {
        if ($unit === 'per_1m_tokens_unified') {
            if ($t->total() === 0) return DecimalMoney::zero();
            return DecimalMoney::fromDecimalString((string)$t->total())
                ->multiply(DecimalMoney::fromDecimalString($rateSpec['any'] ?? '0'))
                ->divideByInteger(self::TOKENS_PER_UNIT);
        }
        return DecimalMoney::zero()
            ->add(self::term($t->input,      $rateSpec['input']      ?? '0'))
            ->add(self::term($t->output,     $rateSpec['output']     ?? '0'))
            ->add(self::term($t->cacheRead,  $rateSpec['cache_read'] ?? '0'))
            ->add(self::term($t->cacheWrite, $rateSpec['cache_write']?? '0'));
    }

    private static function term(int $tokens, string $rate): DecimalMoney
    {
        if ($tokens === 0) return DecimalMoney::zero();
        return DecimalMoney::fromDecimalString((string)$tokens)
            ->multiply(DecimalMoney::fromDecimalString($rate))
            ->divideByInteger(self::TOKENS_PER_UNIT);
    }
}
