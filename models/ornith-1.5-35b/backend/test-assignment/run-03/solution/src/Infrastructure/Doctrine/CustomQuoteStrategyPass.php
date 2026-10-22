<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use App\Infrastructure\Doctrine\CustomQuoteStrategy;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Регистрирует кастомную QuoteStrategy для Doctrine ORM.
 *
 * В DBAL/ORM нового семейства зарезервированные имена столбцов («user») не
 * экранируются автоматически в INSERT/UPDATE, которые генерирует перстинор.
 * Метод setQuoteStrategy на конфигурации ORM применяется ко всем entity
 * менеджерам, поэтому достаточно одного прохода.
 */
final class CustomQuoteStrategyPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasDefinition('doctrine.orm.default_configuration')) {
            return;
        }

        $definition = $container->getDefinition('doctrine.orm.default_configuration');
        $definition->addMethodCall('setQuoteStrategy', [new Reference(CustomQuoteStrategy::class)]);
    }
}
