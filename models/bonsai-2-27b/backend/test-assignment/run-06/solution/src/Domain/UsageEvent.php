<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Событие использования LLM (строка из usage.jsonl).
 *
 * Естественный ключ: (provider, provider_request_id) — доменное
 * событие, которое дедуплицируется при импорте.
 */
final class UsageEvent
{
    public string $provider;
    public string $providerRequestId;
    public string $model;
    public string $user;
    /** Момент запроса (UTC), строка ISO-8601. */
    public string $ts;
    public int $tokensInput;
    public int $tokensOutput;
    public int $tokensCacheRead;
    public int $tokensCacheWrite;

    /**
     * Итоговая стоимость события (USD). null — если событие ещё не
     * рассчитано (unrated).
     */
    public ?string $cost;

    /**
     * Идентификатор строки цены, по которой тарифицировалось.
     * null, если событие unrated.
     */
    public ?int $priceId;

    /**
     * Статус: 0 = unrated (не рассчитан), 1 = rated.
     */
    public int $status;

    public static function fromRow(array $row): self
    {
        $event = new self();
        $event->provider = (string) $row['provider'];
        $event->providerRequestId = (string) $row['provider_request_id'];
        $event->model = (string) $row['model'];
        $event->user = (string) $row['user'];
        $event->ts = (string) $row['ts'];
        $event->tokensInput = (int) $row['tokens_input'];
        $event->tokensOutput = (int) $row['tokens_output'];
        $event->tokensCacheRead = (int) $row['tokens_cache_read'];
        $event->tokensCacheWrite = (int) $row['tokens_cache_write'];
        $event->cost = null;
        $event->priceId = null;
        $event->status = 0; // unrated
        return $event;
    }
}
