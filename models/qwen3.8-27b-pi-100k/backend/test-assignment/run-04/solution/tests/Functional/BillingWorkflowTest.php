<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Интеграционный прогон полного сценария на отдельной тестовой БД (см. .env.test):
 * импорт цен → импорт событий → тарификация → CSV-отчёт «байт в байт»,
 * идемпотентность повторов, unrated при поздних ценах, reprice, ошибки ввода.
 */
final class BillingWorkflowTest extends KernelTestCase
{
    private const EXPECTED_REPORT = "user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\n"
        . "u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499\n"
        . "u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000\n"
        . "u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000\n"
        . "TOTAL,,6200000,2300000,2500000,83333,33.662499\n";

    /** @var string[] Таблицы для очистки (порядок: зависимые первыми) */
    private const TABLES = ['usage_events', 'price_rows', 'models', 'providers'];

    private static ?Application $app = null;
    private static ?EntityManagerInterface $em = null;

    private static function app(): Application
    {
        if (self::$app === null) {
            $kernel = static::createKernel();
            $kernel->boot();

            self::$app = new Application($kernel);
        }

        return self::$app;
    }

    private static function em(): EntityManagerInterface
    {
        if (self::$em === null) {
            if (self::$app === null) {
                self::app();
            }

            /** @var \Doctrine\Bundle\DoctrineBundle\Registry $registry */
            $registry = self::$app->getKernel()->getContainer()->get('doctrine');
            self::$em = $registry->getManager();
        }

        return self::$em;
    }

    protected function setUp(): void
    {
        self::em()->getConnection()->executeStatement(
            \sprintf('TRUNCATE %s RESTART IDENTITY CASCADE', \implode(', ', self::TABLES))
        );

        self::em()->clear();
    }

    public function test_full_acceptance_flow(): void
    {
        $prices = \dirname(__DIR__, 2) . '/fixtures/prices.json';
        $usage = \dirname(__DIR__, 2) . '/fixtures/usage.jsonl';

        $display = $this->command('app:prices:import', ['file' => $prices]);
        self::assertStringContainsString('providers=2', $display);
        self::assertStringContainsString('price_rows=4', $display);

        $display = $this->command('app:usage:import', ['file' => $usage]);
        self::assertStringContainsString('3 новых', $display);

        self::assertStringStartsWith('RATED=3 UNRATED=0', \trim($this->command('app:billing:rate', [])));

        // Отчёт «байт в байт» (см. пример в TASK.md)
        self::assertSame(self::EXPECTED_REPORT, $this->report());

        // Повторный импорт — всё дубликаты, rate ничего не делает, отчёт не меняется
        $display = $this->command('app:usage:import', ['file' => $usage]);
        self::assertStringContainsString('3 дубл', $display);

        self::assertStringStartsWith('RATED=0 UNRATED=0', \trim($this->command('app:billing:rate', [])));
        self::assertSame(self::EXPECTED_REPORT, $this->report());
    }

    public function test_event_before_prices_is_unrated_then_rated_on_rerun(): void
    {
        $prices = \dirname(__DIR__, 2) . '/fixtures/prices.json';
        $late = \dirname(__DIR__, 2) . '/fixtures/prices_late_sfera.json';

        $usage = $this->usageFile([
            ['provider' => 'sfera', 'provider_request_id' => 'early-1', 'model' => 'gyro-pro', 'user' => 'u-dave',
             'ts' => '2026-06-15T09:00:00Z', 'tokens_input' => 1000000, 'tokens_output' => 0,
             'tokens_cache_read' => 0, 'tokens_cache_write' => 0],
        ]);

        $this->command('app:prices:import', ['file' => $prices]);
        $this->command('app:usage:import', ['file' => $usage]);

        $display = $this->command('app:billing:rate', []);
        self::assertMatchesRegularExpression('/RATED=0\s+UNRATED=1/', $display);

        // Появляется цена, действовавшая в момент события: повторная rate считает его.
        $this->command('app:prices:import', ['file' => $late]);
        self::assertStringStartsWith('RATED=1 UNRATED=0', \trim($this->command('app:billing:rate', [])));

        self::assertStringContainsString(
            'u-dave,sfera/gyro-pro,1000000,0,0,0,2.400000',
            $this->report(),
        );
    }

    public function test_reprice_updates_costs_and_report_without_duplicates(): void
    {
        $prices = \dirname(__DIR__, 2) . '/fixtures/prices.json';
        $usage = \dirname(__DIR__, 2) . '/fixtures/usage.jsonl';
        $fixed = \dirname(__DIR__, 2) . '/fixtures/prices_fixed_lumen.json';

        $this->command('app:prices:import', ['file' => $prices]);
        $this->command('app:usage:import', ['file' => $usage]);
        $this->command('app:billing:rate', []);

        // Исправление: выходная ставка lumen-8 (строка с 2026-09-15) 8.0 → 14.0.
        // Затронут только e2 (использует эту строку): 1M × (14.0 − 8.0) = +6.0.
        $display = \trim($this->command('app:billing:reprice', ['file' => $fixed]));
        self::assertStringContainsString('REPRICED_EVENTS=1', $display);
        self::assertStringContainsString('REPRICE_DELTA_USD=6.000000', $display);

        // Отчёт пересчитан: u-bob 11.0 → 17.0, итог 33.662499 → 39.662499.
        $report = $this->report();
        self::assertStringContainsString('u-bob,prism/lumen-8,2000000,1000000,0,0,17.000000', $report);
        self::assertStringContainsString('u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499', $report);
        self::assertStringContainsString('TOTAL,,6200000,2300000,2500000,83333,39.662499', $report);
        self::assertSame(1, \substr_count($report, 'u-bob,prism/lumen-8'), 'без дублей строк');
    }

    public function test_invalid_input_files_are_rejected(): void
    {
        \file_put_contents('/tmp/billing_bad_prices.json', '{invalid');
        \file_put_contents('/tmp/billing_bad_usage.jsonl', "not-json\n");

        self::assertNotSame(0, $this->exitCode('app:prices:import', ['file' => '/tmp/billing_bad_prices.json']));
        self::assertNotSame(0, $this->exitCode('app:usage:import', ['file' => '/tmp/billing_bad_usage.jsonl']));
    }

    // --- вспомогательные --------------------------------------------------------

    /** Записывает события в временный JSONL-файл. */
    private function usageFile(array $events): string
    {
        $path = \sys_get_temp_dir() . '/billing_usage_' . \bin2hex(\random_bytes(4)) . '.jsonl';
        \file_put_contents(
            $path,
            \implode("\n", \array_map(static fn (array $e): string => \json_encode($e, \JSON_UNESCAPED_UNICODE), $events)) . "\n",
        );

        return $path;
    }

    /** Запускает команду, требует exit 0, возвращает текст вывода. */
    private function command(string $name, array $args): string
    {
        $result = $this->runCommand($name, $args);
        self::assertSame(0, $result->getStatusCode(), $result->getDisplay());

        return $result->getDisplay();
    }

    private function exitCode(string $name, array $args): int
    {
        return $this->runCommand($name, $args)->getStatusCode();
    }

    /** CSV-отчёт: команда печатает в реальный stdout (php://stdout) — в тестах используем --out. */
    private function report(): string
    {
        $path = \sys_get_temp_dir() . '/billing_report_' . \bin2hex(\random_bytes(4)) . '.csv';
        $result = $this->runCommand('app:billing:report', ['--from' => '2026-01-01T00:00:00Z', '--to' => '2027-01-01T00:00:00Z', '--out' => $path]);

        self::assertSame(0, $result->getStatusCode(), $result->getDisplay());

        $csv = \file_get_contents($path);
        \unlink($path);

        return \is_string($csv) ? $csv : '';
    }

    /** @param array<string, string> $named аргументы/опции по именам */
    private function runCommand(string $name, array $named): CommandTester
    {
        $tester = new CommandTester(self::app()->find($name));
        $tester->execute($named);

        return $tester;
    }
}
