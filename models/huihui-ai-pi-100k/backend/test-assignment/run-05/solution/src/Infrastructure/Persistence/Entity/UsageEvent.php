<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Entity;

use App\Infrastructure\Persistence\Repository\UsageEventRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования токенов; после тарификации получает
 * cost (bcmath-строка, 6 знаков) и метку применённой строки справочника.
 *
 * Правило идемпотентности (TASK.md, п. 5): событие дедуплицируется
 * по (provider, provider_request_id).
 */
#[ORM\Entity(repositoryClass: UsageEventRepository::class)]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uk_usage_events', columns: ['provider', 'request_id'])]
#[ORM\Index(name: 'idx_usage_events_status_ts', columns: ['status', 'ts'])]
final class UsageEvent
{
    public const STATUS_UNRATED = 'unrated';

    public const STATUS_RATED = 'rated';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id = 0;

    #[ORM\Column(type: 'string', length: 64)]
    public string $provider = '';

    /** Экстренный ключ дедупликации (в JSON он называется provider_request_id). */
    #[ORM\Column(type: 'string', length: 255)]
    public string $requestId = '';

    #[ORM\Column(type: 'string', length: 64)]
    public string $model = '';

    #[ORM\Column(type: 'string', length: 255)]
    public string $user = '';

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $ts;

    #[ORM\Column(options: ['default' => 0])]
    public int $tokensInput = 0;

    #[ORM\Column(options: ['default' => 0])]
    public int $tokensOutput = 0;

    #[ORM\Column(options: ['default' => 0])]
    public int $tokensCacheRead = 0;

    #[ORM\Column(options: ['default' => 0])]
    public int $tokensCacheWrite = 0;

    /** unrated | rated */
    #[ORM\Column(type: 'string', length: 16)]
    public string $status = self::STATUS_UNRATED;

    /** Стоимость события, bcmath-строка с 6 знаками; NULL для unrated. */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    public ?string $cost = null;

    /** Ссылка на строку справочника (datetime valid_from), применённую при тарификации. */
    #[ORM\Column(name: 'price_valid_from', type: 'datetime_immutable', nullable: true)]
    public ?\DateTimeImmutable $priceValidFrom = null;

    public function __construct()
    {
    }
}
