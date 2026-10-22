<?php

declare(strict_types=1);

namespace App\Tests\Console;

use App\Command\ImportPricesCommand;
use App\Command\ImportUsageCommand;
use App\Command\RateCommand;
use App\Command\RepriceCommand;
use App\Command\ReportCommand;
use App\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Сквозной сценарий: чистая схема -> импорт цен/событий -> rate -> report.
 */
final class BillingFlowTest extends TestCase
{
    private Kernel $kernel;
    private string $projectDir;

    protected function setUp(): void
    {
        $_SERVER['APP_ENV'] = 'test';
        $_ENV['APP_ENV'] = 'test';

        $this->kernel = new Kernel('test', false);
        $this->kernel->boot();

        $em = $this->em();
        $conn = $em->getConnection();

        // Чистая схема в каждом тесте.
        $conn->executeStatement('DROP SCHEMA public CASCADE');
        $conn->executeStatement('CREATE SCHEMA public');
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());
        $em->clear();

        $this->projectDir = $this->kernel->getProjectDir();
    }

    protected function tearDown(): void
    {
        $this->kernel->shutdown();
        parent::tearDown();
    }

    public function testFixturesFlowMatchesSpec(): void
    {
        [$code] = $this->runCmd(['app:prices:import', 'file' => $this->projectDir . '/fixtures/prices.json']);
        self::assertSame(0, $code, 'prices import should exit 0');

        [$code] = $this->runCmd(['app:usage:import', 'file' => $this->projectDir . '/fixtures/usage.jsonl']);
        self::assertSame(0, $code, 'usage import should exit 0');

        [$code, $stdout] = $this->runCmd(['app:billing:rate']);
        self::assertSame(0, $code, 'rate should exit 0');
        self::assertSame('RATED=3 UNRATED=0', trim($stdout));

        $this->em()->clear();

        $expected = <<<'CSV'
        user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
        u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
        u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
        u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
        TOTAL,,6200000,2300000,2500000,83333,33.662499
        CSV;

        $report = $this->reportBetween('2026-09-01T00:00:00Z', '2026-10-01T00:00:00Z');

        self::assertSame($expected, $report);
    }

    public function testIdempotentReimportDoesNotChangeReport(): void
    {
        $this->setupBaseline();
        $first = $this->reportBetween('2026-09-01T00:00:00Z', '2026-10-01T00:00:00Z');

        // Повторный импорт тех же файлов.
        $this->em()->clear();
        $this->setupBaseline();
        $second = $this->reportBetween('2026-09-01T00:00:00Z', '2026-10-01T00:00:00Z');

        self::assertSame($first, $second);
    }

    public function testRepriceUpdatesCostsAndReport(): void
    {
        $this->setupBaseline();

        // Коррекция цены: input 3.0 -> 2.0.
        $file = $this->projectDir . '/tmp_correction';
        $jsonPayload = json_encode([
            'prices' => [[
                'provider' => 'prism',
                'model' => 'lumen-8',
                'valid_from' => '2026-08-01T00:00:00Z',
                'currency' => 'USD',
                'unit' => 'per_1m_tokens',
                'rates' => [
                    'input' => '2.000000',
                    'output' => '12.000000',
                    'cache_read' => '0.300000',
                    'cache_write' => '3.750000',
                ],
            ]],
        ]);
        self::assertIsString($jsonPayload);
        file_put_contents($file, $jsonPayload);

        [$code, $stdout] = $this->runCmd(['app:billing:reprice', 'file' => $file]);
        @unlink($file);

        self::assertSame(0, $code, 'reprice should exit 0');
        $lines = array_values(array_filter(array_map('trim', explode("\n", trim($stdout)))));
        self::assertSame(['REPRICED_EVENTS=1', 'REPRICE_DELTA_USD=-1.200000'], $lines);

        $this->em()->clear();
        $report = $this->reportBetween('2026-09-01T00:00:00Z', '2026-10-01T00:00:00Z');

        self::assertStringContainsString('u-alice,prism/lumen-8,1200000,800000,2500000,83333,13.062499', $report);
        self::assertStringNotContainsString('14.262499', $report);
    }

    private function setupBaseline(): void
    {
        $this->runCmd(['app:prices:import', 'file' => $this->projectDir . '/fixtures/prices.json']);
        $this->runCmd(['app:usage:import', 'file' => $this->projectDir . '/fixtures/usage.jsonl']);
        $this->runCmd(['app:billing:rate']);
        $this->em()->clear();
    }

    private function reportBetween(string $from, string $to): string
    {
        [$code, $out] = $this->runCmd([
            'app:billing:report',
            '--from' => $from,
            '--to' => $to,
        ]);

        self::assertSame(0, $code, "report should exit 0, got {$code}: {$out}");

        return trim($out);
    }

    /**
     * @return array{0: int, 1: string}
     */
    private function runCmd(array $args): array
    {
        $application = $this->makeConsoleApp();
        $input = new ArrayInput($args);
        $output = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true);
        $code = $application->run($input, $output);

        return [$code, $output->fetch()];
    }

    private function makeConsoleApp(): Application
    {
        $container = $this->kernel->getContainer()->get('test.service_container');

        $app = new Application('app', '1.0');
        $app->setAutoExit(false);
        $app->addCommands([
            $container->get(ImportPricesCommand::class),
            $container->get(ImportUsageCommand::class),
            $container->get(RateCommand::class),
            $container->get(ReportCommand::class),
            $container->get(RepriceCommand::class),
        ]);

        return $app;
    }

    private function em(): EntityManagerInterface
    {
        return $this->kernel->getContainer()->get('doctrine')->getManager();
    }
}
