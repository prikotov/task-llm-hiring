<?php

declare(strict_types=1);

namespace App\Import;

/** Счётчики изменений при импорте — для служебных сообщений в stderr. */
final class Result
{
    public function __construct(
        private int $providers = 0,
        private int $models = 0,
        private int $priceRows = 0,
        private int $events = 0,
    ) {
    }

    public function touchedProvider(): self
    {
        $this->providers++;

        return $this;
    }

    public function touchedModel(): self
    {
        $this->models++;

        return $this;
    }

    public function touchedPriceRow(): self
    {
        $this->priceRows++;

        return $this;
    }

    public function importedEvent(): self
    {
        $this->events++;

        return $this;
    }

    public function providerCount(): int
    {
        return $this->providers;
    }

    public function modelCount(): int
    {
        return $this->models;
    }

    public function priceRowCount(): int
    {
        return $this->priceRows;
    }

    public function eventCount(): int
    {
        return $this->events;
    }

    public function summary(string $label): string
    {
        return match ($label) {
            'prices' => sprintf(
                'Импорт справочника: providers=%d models=%d price_rows=%d',
                $this->providers,
                $this->models,
                $this->priceRows,
            ),
            'usage' => sprintf('Импорт событий: новых=%d', $this->events),
            default => throw new \InvalidArgumentException($label),
        };
    }
}
