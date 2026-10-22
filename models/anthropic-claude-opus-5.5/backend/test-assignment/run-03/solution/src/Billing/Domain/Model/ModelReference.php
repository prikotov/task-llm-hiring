<?php

declare(strict_types=1);

namespace App\Billing\Domain\Model;

/**
 * Ссылка на модель провайдера: (provider code, model code).
 * Модели двух провайдеров с одинаковым кодом — разные модели.
 */
final readonly class ModelReference implements \Stringable
{
    public function __construct(
        public string $providerCode,
        public string $modelCode,
    ) {
        if ('' === trim($providerCode) || '' === trim($modelCode)) {
            throw new \InvalidArgumentException('Код провайдера и код модели обязательны');
        }
    }

    public function equals(self $other): bool
    {
        return $this->providerCode === $other->providerCode
            && $this->modelCode === $other->modelCode;
    }

    /**
     * Отображаемое имя модели в отчёте: "<provider>/<code>".
     */
    public function __toString(): string
    {
        return $this->providerCode.'/'.$this->modelCode;
    }
}
