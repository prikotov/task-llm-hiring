<?php

declare(strict_types=1);

namespace App\Domain\Billing\Exception;

/**
 * Ошибка валидации/импорта данных тарифа или событий. Печатается командой
 * в stderr, код завершения — 1.
 *
 * @extends \RuntimeException
 */
class BillingException extends \RuntimeException
{
}
