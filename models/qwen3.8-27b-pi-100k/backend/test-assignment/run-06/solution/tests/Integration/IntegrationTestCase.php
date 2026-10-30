<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Базовый класс интеграционных тестов:
 * - поднимает ядро (APP_ENV=test, база из env DATABASE_URL);
 * - до каждого теста применяет миграции и обнуляет таблицы;
 * - после теста глушит ядро.
 *
 * Отдельная БД для тестов не требуется: сценарии идут на
 * «проектной» базе 127.0.0.1:5544/app и каждый тест стартует с
 * чистого состояния (см. truncateTables).
 */
abstract class IntegrationTestCase extends KernelTestCase
{
    private const TRUNCATE_ORDER = [
        'usage_events',
        'price_rows',
        'models',
        'providers',
    ];

    protected \Doctrine\ORM\EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();

        if (!self::$booted) {
            self::bootKernel();
        }

        $container = self::getContainer();
        $em = $container->get('doctrine')->getManager();
        self::assertInstanceOf(\Doctrine\ORM\EntityManagerInterface::class, $em);
        $this->em = $em;

        $this->truncateTables();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        self::ensureKernelShutdown();
    }

    protected function truncateTables(): void
    {
        foreach (self::TRUNCATE_ORDER as $table) {
            $this->em->getConnection()->executeStatement('TRUNCATE '.$table.' RESTART IDENTITY CASCADE');
        }
    }

    /**
     * Путь к фиксуре из fixtures/.
     */
    protected function fixture(string $name): string
    {
        return dirname(__DIR__, 2).'/fixtures/'.$name;
    }
}
