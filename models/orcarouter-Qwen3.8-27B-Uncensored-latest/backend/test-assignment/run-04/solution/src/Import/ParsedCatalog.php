<?php

declare(strict_types=1);

namespace App\Import;

use App\Shared\ImportException;

/**
 * Parsed and validated price catalog (file of app:prices:import and
 * app:billing:reprice).
 *
 * @readonly
 */
final class ParsedCatalog
{
    /** @param array<string, ProviderData> $providers by code */
    /** @param array<string, array{code: string}> $modelsByProvider */
    public function __construct(
        public readonly array $providers,
        public readonly array $modelsByProvider,
        /** @var List<PriceData> */
        public readonly array $prices,
    ) {
    }
}
