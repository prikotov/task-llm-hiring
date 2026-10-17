<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Money;
use App\Domain\TokenUsage;
use App\Domain\UsageEventStatus;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

/**
 * Событие использования (журнал, поставляемый провайдером).
 *
 * Естественный ключ — (provider, providerRequestId): повторная доставка
 * уже известного события игнорируется целиком (никаких обновлений).
 *
 * provider/model — «снимок» значений из потока событий; связь со
 * справочником цен (providers/models/prices) — по этим значениям в момент
 * тарификации. Это позволяет принимать события до появления справочника —
 * такие события остаются unrated.
 *
 * Статус события выводится из данных:
 *   rated  — cost заполнен (и задан price),
 *   unrated — cost пуст (строка цен не была найдена к моменту тарификации).
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
final class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    public ?int $id = null;

    /** Код провайдера из потока событий (соответствует providers.code). */
    #[ORM\Column(name: 'provider_code', length: 128)]
    public string $provider = '';

    /** Идентификатор запроса со стороны провайдера. */
    #[ORM\Column(name: 'provider_request_id', length: 128)]
    public string $providerRequestId = '';

    /** Код модели из потока событий (соответствует models.code). */
    #[ORM\Column(name: 'model_code', length: 128)]
    public string $model = '';

    #[ORM\Column(name: 'username', length: 128)]
    public string $user = '';

    /** Момент запроса (UTC). */
    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    public \DateTimeImmutable $ts;

    #[ORM\Column(type: Types::INTEGER)]
    public int $tokensInput = 0;

    #[ORM\Column(type: Types::INTEGER)]
    public int $tokensOutput = 0;

    #[ORM\Column(type: Types::INTEGER)]
    public int $tokensCacheRead = 0;

    #[ORM\Column(type: Types::INTEGER)]
    public int $tokensCacheWrite = 0;

    /** Стоимость события в USD (6 знаков), заполняется при тарификации. */
    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 6, nullable: true)]
    public ?string $cost = null;

    /** Строка цен, по которой событие рассчитано (null, пока unrated). */
    #[ORM\ManyToOne(targetEntity: Price::class)]
    #[ORM\JoinColumn(name: 'price_id', nullable: true)]
    public ?Price $price = null;

    public function status(): UsageEventStatus
    {
        return $this->cost !== null ? UsageEventStatus::Rated : UsageEventStatus::Unrated;
    }

    public function tokenUsage(): TokenUsage
    {
        return new TokenUsage($this->tokensInput, $this->tokensOutput, $this->tokensCacheRead, $this->tokensCacheWrite);
    }

    /** Фиксирует результат тарификации (стоимость — уже округлённая до 6 знаков). */
    public function markRated(Money $cost, Price $price): void
    {
        $this->cost = $cost->toDecimalString();
        $this->price = $price;
    }

    /** Стоимость события в Money (0 при unrated). */
    public function costMoney(): Money
    {
        return $this->cost === null ? Money::zero() : Money::fromDecimalString($this->cost);
    }
}
