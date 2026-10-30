<?php

declare(strict_types=1);

namespace App\Domain;

/** Ошибка при проверке/считывании схемы тарификации или ставок. */
final class PricingSchemaException extends \InvalidArgumentException
{
}
