<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

abstract class BillingIntegrationTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->truncateAll();
    }

    protected function tearDown(): void
    {
        self::bootKernel(); // гарантируем, что контейнер закрыт между тестами
        parent::tearDown();
    }

    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    private function truncateAll(): void
    {
        $conn = self::getContainer()->get('doctrine.dbal.default_connection');
        $conn->executeStatement('TRUNCATE usage_events, price_rows, models, providers RESTART IDENTITY');
    }

    /**
     * Выполняет консольную команду (служба с именем = FQCN класса) и возвращает CommandTester.
     */
    protected function tester(string $command, array $params = []): CommandTester
    {
        $commandInstance = self::getContainer()->get($command);
        $tester = new CommandTester($commandInstance);
        $tester->execute($params);

        return $tester;
    }

    protected function fixturePath(string $name): string
    {
        return dirname(__DIR__, 2).'/fixtures/'.$name;
    }
}
