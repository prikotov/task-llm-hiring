<?php

declare(strict_types=1);

namespace App\Domain\Billing\Entity;

use App\Domain\Billing\Enum\PriceUnit;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (фактический запрос к модели), поставленное провайдером.
 *
 * Естественный ключ — (provider, provider_request_id): по нему дедуплицируется
 * повторные доставки. Платежные (суммы/стоимости) считаются тарифицирующим
 * процессом, поэтому здесь хранятся входные данные + статус и результат
 * тарификации (cost — строка, а не float).
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_event_provider_request', columns: ['provider', 'request_id'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $provider = '';

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 128)]
    public string $requestId = '';

    #[ORM\Column(type: 'string', length: 64)]
    public string $model = '';

    #[ORM\Column(name: '"user"', type: 'string', length: 128)]
    public string $user = '';

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $ts;

    #[ORM\Column(type: 'integer')]
    public int $tokensInput = 0;

    #[ORM\Column(type: 'integer')]
    public int $tokensOutput = 0;

    #[ORM\Column(type: 'integer')]
    public int $tokensCacheRead = 0;

    #[ORM\Column(type: 'integer')]
    public int $tokensCacheWrite = 0;

    /** false — событие ещё не рассчитано (unrated). */
    #[ORM\Column(type: 'boolean')]
    public bool $rated = false;

    /**
     * valid_from строки цен, по которой событие рассчитано (для пересчёта
     * при reprice). Пусто для unrated-событий.
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    public ?\DateTimeImmutable $appliedValidFrom = null;

    /** Итоговая стоимость, уже округлённая до 6 знаков (HALF_UP). */
    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    public ?string $cost = null;

    public function __construct(
        string $provider,
        string $requestId,
        string $model,
        string $user,
        \DateTimeImmutable $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ) {
        $this->provider = $provider;
        $this->requestId = $requestId;
        $this->model = $model;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
    }

    public function markRated(\DateTimeImmutable $appliedValidFrom, string $cost): void
    {
        $this->rated = true;
        $this->appliedValidFrom = $appliedValidFrom;
        $this->cost = $cost;
    }
}
