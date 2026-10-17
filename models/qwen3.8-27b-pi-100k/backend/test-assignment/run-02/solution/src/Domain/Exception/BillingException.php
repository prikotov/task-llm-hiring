<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use RuntimeException;

/**
 * Общее доменное исключение: некорректные входные данные
 * или нарушение правила тарификации.
 */
final class BillingException extends RuntimeException
{
}
