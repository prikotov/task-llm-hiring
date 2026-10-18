<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Некорректная ретро-коррекция цен (провайдер/модель отсутствуют в справочнике).
 */
final class InvalidRepriceFileException extends BillingException
{
}
