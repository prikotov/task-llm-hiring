<?php

declare(strict_types=1);

namespace App\Application;

use App\Infrastructure\Input;
use App\Infrastructure\PriceCatalog;
use App\Infrastructure\WriteTransaction;

final class ImportPrices
{
    public function __construct(private Input $input, private PriceCatalog $catalog, private WriteTransaction $transaction)
    {
    }

    public function run(string $file): void
    {
        $document = $this->input->prices($file);
        $this->transaction->run(fn () => $this->catalog->import($document));
    }
}
