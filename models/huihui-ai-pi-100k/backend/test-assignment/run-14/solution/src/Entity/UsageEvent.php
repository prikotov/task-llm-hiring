<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\EventKey;
use App\Domain\UsageTokens;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (одна строка журналового потока поставщика).
 *
 * Естественный ключ: (provider, providerRequestId) — по нему дедуплицируется
 * импорт. `status` управляет жизненным циклом расчёта стоимости:
 *
 *  pending    — ещё не пробовали расчитать (новый импорт)
 *  rated      — стоимость зафиксирована (`costUsd`)
 *  unrated    — не нашлась применимая цена в этот раз (`pricedBy == null`)
 *  replaced   — стоимость снята (событие заменено reprice-заменой)
 *
 * `replaces` — ссылка на событие, которое было заменено этим (цепочка
 * reprice-истории). В отчёте участвует только последнее звено цепочки с
 * `status = 'rated'`; заменённые звена (status = 'replaced') исключаются.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uq_usage_provider_event', columns: ['provider_code', 'provider_request_id']),
    new ORM\UniqueConstraint(name: 'uq_usage_replaces_event', columns: ['replaces_event_id']),
], indexes: [
    new ORM\Index(name: 'idx_usage_ts', columns: ['ts']),
    new ORM\Index(name: 'idx_usage_status', columns: ['status']),
])]
final class UsageEvent
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RATED   = 'rated';
    public const STATUS_UNRATED = 'unrated';
    public const STATUS_REPLACED = 'replaced';

    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: 'integer')]
        private int|null $id = null,

        #[ORM\Column(name: 'provider_code', type: 'string', length: 64)]
        public string $provider = '',

        #[ORM\Column(name: 'provider_request_id', type: 'string', length: 128)]
        public string $providerRequestId = '',

        #[ORM\Column(type: 'string', length: 64)]
        public string $model = '',

        #[ORM\Column(name: 'user_id', type: 'string', length: 128)]
        public string $user = '',

        /** Момент запроса (UTC) — включительно в отчётный период. */
        #[ORM\Column(type: 'datetime_immutable')]
        public \DateTimeInterface $ts = new \DateTimeImmutable('1970-01-01T00:00:00Z'),

        #[ORM\Column(type: 'integer', options: ['default' => 0])]
        public int $tokensInput = 0,

        #[ORM\Column(type: 'integer', options: ['default' => 0])]
        public int $tokensOutput = 0,

        #[ORM\Column(type: 'integer', options: ['default' => 0])]
        public int $tokensCacheRead = 0,

        #[ORM\Column(type: 'integer', options: ['default' => 0])]
        public int $tokensCacheWrite = 0,

        /** self::STATUS_*. */
        #[ORM\Column(type: 'string', length: 16, options: ['default' => self::STATUS_PENDING])]
        public string $status = self::STATUS_PENDING,

        /** Десятичная строка (bcmath, scale 6); значимо только при status = 'rated'. */
        #[ORM\Column(name: 'cost_usd', type: 'string', length: 32)]
        public string $costUsd = '0.000000',

        /** Строка цен, которой тарифицировано событие (nullable; null при unrated/replaced). */
        #[ORM\ManyToOne(targetEntity: Price::class)]
        #[ORM\JoinColumn(name: 'priced_by', referencedColumnName: 'id', nullable: true)]
        public Price|null $pricedBy = null,

        /** Событие, которое было заменено этим (цепочка reprice). */
        #[ORM\ManyToOne(targetEntity: self::class)]
        #[ORM\JoinColumn(name: 'replaces_event_id', referencedColumnName: 'id', nullable: true)]
        public self|null $replaces = null,
    ) {
    }

    public function key(): EventKey
    {
        return new EventKey($this->provider, $this->providerRequestId);
    }

    public function tokens(): UsageTokens
    {
        return new UsageTokens(
            $this->tokensInput,
            $this->tokensOutput,
            $this->tokensCacheRead,
            $this->tokensCacheWrite,
        );
    }

    /** Момент действия события (UTC). */
    public function tsUtc(): \DateTimeImmutable
    {
        return $this->ts instanceof \DateTimeImmutable
            ? $this->ts
            : new \DateTimeImmutable($this->ts->format('Y-m-d\TH:i:s\Z'), new \DateTimeZone('UTC'));
    }

    /** Включительно в период [from, to). null-значения = без ограничения. */
    public function inPeriod(?\DateTimeInterface $from, ?\DateTimeInterface $to): bool
    {
        $t = $this->tsUtc();
        if ($from !== null && $t < $from) {
            return false;
        }
        if ($to !== null && $t >= $to) {
            return false;
        }

        return true;
    }

    public function isRated(): bool
    {
        return $this->status === self::STATUS_RATED;
    }

    /** Входит ли событие в итоговый отчёт: rated, и не заменено другим звеном. */
    public function isFinalRated(): bool
    {
        return $this->status === self::STATUS_RATED;
    }
}
