<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Decimal;
use App\Domain\Entity\UsageEvent;
use App\Domain\Repository\UsageEventRepository;

/**
 * Формирование сводного отчёта по тарифицированным событиям.
 *
 * Группирует события по (пользователь, модель); cost_usd — сумма уже
 * округлённых стоимостей событий группы, без повторного округления.
 */
final class Report
{
    /** @var array{user:string,model:string,input:int,output:int,cache_read:int,cache_write:int,cost:Decimal}[] */
    private array $rows = [];

    /** @var Decimal */
    private $totalInput;
    /** @var Decimal */
    private $totalOutput;
    /** @var Decimal */
    private $totalCacheRead;
    /** @var Decimal */
    private $totalCacheWrite;
    /** @var Decimal */
    private $totalCost;

    public function __construct(private UsageEventRepository $events)
    {
        $this->totalInput = new Decimal('0');
        $this->totalOutput = new Decimal('0');
        $this->totalCacheRead = new Decimal('0');
        $this->totalCacheWrite = new Decimal('0');
        $this->totalCost = new Decimal('0');
    }

    public function build(
        ?\DateTimeImmutable $from = null,
        ?\DateTimeImmutable $to = null,
    ): void {
        /** @var UsageEvent[] $events */
        $events = $this->events->findRated($from, $to);

        foreach ($events as $event) {
            $key = $event->user . "\x1f" . $event->model;
            if (!isset($this->rows[$key])) {
                $this->rows[$key] = [
                    'user' => $event->user,
                    'model' => $event->model,
                    'input' => 0,
                    'output' => 0,
                    'cache_read' => 0,
                    'cache_write' => 0,
                    'cost' => new Decimal((string) $event->costUsd),
                ];
            } else {
                $this->rows[$key]['input'] += $event->tokensInput;
                $this->rows[$key]['output'] += $event->tokensOutput;
                $this->rows[$key]['cache_read'] += $event->tokensCacheRead;
                $this->rows[$key]['cache_write'] += $event->tokensCacheWrite;
                $this->rows[$key]['cost'] = $this->rows[$key]['cost']->add((string) $event->costUsd);
            }

            $this->totalInput = $this->totalInput->add($event->tokensInput);
            $this->totalOutput = $this->totalOutput->add($event->tokensOutput);
            $this->totalCacheRead = $this->totalCacheRead->add($event->tokensCacheRead);
            $this->totalCacheWrite = $this->totalCacheWrite->add($event->tokensCacheWrite);
            $this->totalCost = $this->totalCost->add((string) $event->costUsd);
        }

        // сортировка по user, затем по model (лексикографически).
        uasort($this->rows, static function (
            array $a,
            array $b,
        ): int {
            $cmp = strcasecmp($a['user'], $b['user']);
            if (0 !== $cmp) {
                $cmp = $cmp <=> 0;
                return $cmp;
            }

            return strcasecmp($a['model'], $b['model']);
        });
    }

    /**
     * @return array{user:string,model:string,input:int,output:int,cache_read:int,cache_write:int,cost:string}[]
     */
    public function rows(): array
    {
        $out = [];
        foreach ($this->rows as $row) {
            $out[] = [
                'user' => $row['user'],
                'model' => $row['model'],
                'input' => $row['input'],
                'output' => $row['output'],
                'cache_read' => $row['cache_read'],
                'cache_write' => $row['cache_write'],
                'cost' => Decimal::halfUp($row['cost']->toString()),
            ];
        }

        return $out;
    }

    public function total(): array
    {
        return [
            'input' => $this->totalInput->toString(),
            'output' => $this->totalOutput->toString(),
            'cache_read' => $this->totalCacheRead->toString(),
            'cache_write' => $this->totalCacheWrite->toString(),
            'cost' => Decimal::halfUp($this->totalCost->toString()),
        ];
    }
}
