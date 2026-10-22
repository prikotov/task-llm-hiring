<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Некорректный файл истории цен (JSON, структура, ставки, окна оф-пик).
 */
final class InvalidPricesFileException extends BillingException
{
}
