<?php

declare(strict_types=1);

namespace App\Application\Ingestion;

use App\Domain\DomainException;

/**
 * Общие парсеры входных данных.
 */
final class Parsers
{
    /**
     * @throws DomainException
     */
    public static function isoUtc(string $v, string $ctx = 'isoUtc'): \DateTimeImmutable
    {
        try {
            $d = new \DateTimeImmutable($v, new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new DomainException("{$ctx}: invalid ISO-8601 instant: \"{$v}\"");
        }
        if ($d instanceof \DateTimeImmutable === false) {
            throw new DomainException("{$ctx}: invalid ISO-8601 instant: \"{$v}\"");
        }
        if ($d->getTimezone()->getName() !== 'UTC') {
            $d = $d->setTimezone(new \DateTimeZone('UTC'));
        }

        return $d;
    }
}
