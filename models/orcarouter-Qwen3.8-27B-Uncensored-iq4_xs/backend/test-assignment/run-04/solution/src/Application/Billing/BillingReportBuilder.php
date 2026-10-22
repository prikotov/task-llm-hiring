<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Money\Money;
use App\Domain\Usage\TokenKind;
use App\Infrastructure\Persistence\UsageEventRepository;

/**
 * Сборка отчёта расходов: только рассчитанные (rated) события,
 * период [from, to) по ts. Группировка — по (user, <provider>/<model>).
 * Суммы стоимости — сумма уже округлённых стоимостей событий,
 * без повторного округления слагаемых.
 */
final class BillingReportBuilder
{
    private const KEY_SEPARATOR = "\x1f";

    public function __construct(
        private readonly UsageEventRepository $events,
    ) {
    }

    public function build(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): BillingReport
    {
        $events = $this->events->findRatedInPeriod($from, $to);

        /** @var array<string, array{user: string, model: string, tokens: array, cost: Money}> $groups */
        $groups = [];
        foreach ($events as $event) {
            $modelKey = $event->model()->qualifiedName();
            $key = $event->user().self::KEY_SEPARATOR.$modelKey;

            $group = $groups[$key] ??= [
                'user' => $event->user(),
                'model' => $modelKey,
                'tokens' => [
                    TokenKind::Input->name => 0,
                    TokenKind::Output->name => 0,
                    TokenKind::CacheRead->name => 0,
                    TokenKind::CacheWrite->name => 0,
                ],
                'cost' => Money::zero(),
            ];

            foreach (TokenKind::all() as $kind) {
                $group['tokens'][$kind->name] += $event->tokenCount($kind);
            }

            $group['cost'] = $group['cost']->add($event->money());
            $groups[$key] = $group;
        }

        // Сортировка по (user, model) побайтово: разделитель \x1f меньше
        // любого печатаемого символа, поэтому лексический порядок ключа
        // совпадает с требуемым порядком пар.
        ksort($groups, SORT_STRING);

        $rows = [];
        $totalTokens = [0, 0, 0, 0];
        $totalCost = Money::zero();
        foreach ($groups as $group) {
            $tokens = [
                $group['tokens'][TokenKind::Input->name],
                $group['tokens'][TokenKind::Output->name],
                $group['tokens'][TokenKind::CacheRead->name],
                $group['tokens'][TokenKind::CacheWrite->name],
            ];
            foreach ($tokens as $i => $value) {
                $totalTokens[$i] += $value;
            }

            $rows[] = new ReportRow(
                $group['user'],
                $group['model'],
                $tokens,
                $group['cost']->asFixed6String(),
            );
            $totalCost = $totalCost->add($group['cost']);
        }

        return new BillingReport($rows, $totalTokens, $totalCost->asFixed6String());
    }
}
