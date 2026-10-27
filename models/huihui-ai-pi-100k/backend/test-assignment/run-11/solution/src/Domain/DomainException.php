<?php

declare(strict_types=1);

namespace App\Domain;

use RuntimeException;

/**
 * Ошибка предметной логики: неконсистентные входные данные, неизвестный
 * провайдер/модель и т.п. Команды преобразуют её в выходной код 1.
 */
class DomainException extends RuntimeException
{
    public function __construct(string $message, private readonly ?\Throwable $cause = null)
    {
        parent::__construct($message, 0, $cause);
    }

    public static function wrap(\Throwable $e, string $context): self
    {
        return $e instanceof self
            ? $e
            : new self($context . ': ' . $e->getMessage(), $e);
    }
}
