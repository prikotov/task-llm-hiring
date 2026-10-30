<?php

declare(strict_types=1);

namespace App\Dbal;

use Doctrine\Common\EventSubscriber;

/**
 * Фиксирует часовой пояс сессии БД в UTC: все timestamp с/без таймзоны
 * интерпретируются в UTC независимо от окружения.
 */
final class UtcConnectionListener implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [\Doctrine\DBAL\Events::onConnect];
    }

    public function onConnect(\Doctrine\DBAL\Event\ConnectionEventArgs $eventArgs): void
    {
        $eventArgs->getDriverConnection()->exec('SET TIME ZONE "UTC"');
    }
}
