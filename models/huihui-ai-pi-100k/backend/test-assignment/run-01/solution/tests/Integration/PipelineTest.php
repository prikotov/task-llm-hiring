<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Domain\Decimal;
use App\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Интеграционные тесты полного пайплайна (импорт справочника → импорт
 * событий → тарификация → отчёт → повторный импорт → reprice)
 * на изолированной in-memory-базе (sqlite) для каждого сценария.
 */
final class PipelineTest extends TestCase
{
    private Kernel $kernel;
    private Application $app;
    private EntityManagerInterface $em;

    private const PRICES = __DIR__ . '/../../fixtures/prices.json';
    private const USAGE = __DIR__ . '/../../fixtures/usage.jsonl';

    #[\Override]
    protected function setUp(): void
    {
        $dsn = 'sqlite:///:memory:';
        putenv('DATABASE_URL=' . $dsn);
        $_SERVER['DATABASE_URL'] = $_ENV['DATABASE_URL'] = $dsn;

        $this->kernel = new \App\Kernel('test', true);
        $this->kernel->boot();
        $container = $this->kernel->getContainer();
        $this->app = new Application($this->kernel);

        $this->em = $container->get('doctrine')->getManager();
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->createSchema($this->em->getMetadataFactory()->getAllMetadata());
    }

    #[\Override]
    protected function tearDown(): void
    {
        $this->kernel->shutdown();
        putenv('DATABASE_URL');
    }

    private function runCommand(string $name, array $args = []): CommandTester
    {
        $command = $this->app->find($name);
        $tester = new CommandTester($command);
        $tester->execute($args, ['interactive' => false]);

        return $tester;
    }

    private function lines(string $display): array
    {
        $lines = preg_split('/\R/', trim($display));

        return array_map('trim', array_filter($lines, static fn (string $l): bool => $l !== ''));
    }

    public function testFullPipelineAndIdempotentRerun(): void
    {
        // 1) Импорт справочника цен.
        $tester = $this->runCommand('app:prices:import', ['file' => self::PRICES]);
        self::assertSame(0, $tester->getStatusCode(), $tester->getDisplay());
        self::assertStringContainsString('Справочник импортирован', $tester->getDisplay());
        self::assertStringContainsString('4', $tester->getDisplay());

        // 2) Импорт событий.
        $tester = $this->runCommand('app:usage:import', ['file' => self::USAGE]);
        self::assertSame(0, $tester->getStatusCode(), $tester->getDisplay());
        self::assertStringContainsString('3', $tester->getDisplay());

        // 3) Тарификация.
        $tester = $this->runCommand('app:billing:rate');
        self::assertSame(0, $tester->getStatusCode(), $tester->getDisplay());
        self::assertSame('RATED=3 UNRATED=0', $this->lines($tester->getDisplay())[0]);

        // 4) Отчёт за июль/август/сентябрь (входящие границы).
        $tester = $this->runCommand('app:billing:report', [
            '--from' => '2026-09-01T00:00:00Z',
            '--to' => '2026-10-01T00:00:00Z',
        ]);
        self::assertSame(0, $tester->getStatusCode(), $tester->getDisplay());
        $expected = <<<'CSV'
            user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
            u-alice,prism/lumen-8,1200000,800000,2500000,83333,14.262499
            u-alice,sfera/gyro-pro,3000000,500000,0,0,8.400000
            u-bob,prism/lumen-8,2000000,1000000,0,0,11.000000
            TOTAL,,6200000,2300000,2500000,83333,33.662499
            CSV;
        self::assertSame(trim($expected), trim($tester->getDisplay()));

        // 5) Повторный импорт справочника и событий — идемпотентность.
        $tester = $this->runCommand('app:usage:import', ['file' => self::USAGE]);
        self::assertSame(0, $tester->getStatusCode(), $tester->getDisplay());
        self::assertMatchesRegularExpression('/\d+/', $tester->getDisplay());

        $tester = $this->runCommand('app:billing:rate');
        self::assertSame(0, $tester->getStatusCode());
        self::assertSame('RATED=0 UNRATED=0', $this->lines($tester->getDisplay())[0]);

        // 6) Отчёт без границ — тот же результат; количество строк событий = 3.
        $tester = $this->runCommand('app:billing:report');
        self::assertSame(0, $tester->getStatusCode(), $tester->getDisplay());
        self::assertSame(trim($expected), trim($tester->getDisplay()));

        $eventCount = $this->em->createQuery('SELECT COUNT(e.id) FROM App\Entity\UsageEvent e')->getSingleScalarResult();
        self::assertSame('3', (string) $eventCount);
    }

    public function testReportPeriodBoundariesAreInclusiveFromExclusiveTo(): void
    {
        $this->runCommand('app:prices:import', ['file' => self::PRICES]);
        $this->runCommand('app:usage:import', ['file' => self::USAGE]);
        $this->runCommand('app:billing:rate');

        // to исключительно: to = ts(e2) 12:00 — e1 (11:00) входит, e2 (12:00) — нет
        $tester = $this->runCommand('app:billing:report', ['--to' => '2026-09-15T12:00:00Z']);
        self::assertSame(0, $tester->getStatusCode(), $tester->getDisplay());
        self::assertStringContainsString('u-alice,prism/lumen-8', $tester->getDisplay());
        self::assertStringNotContainsString('u-bob,prism/lumen-8', $tester->getDisplay());
        self::assertStringNotContainsString('gyro-pro', $tester->getDisplay());

        // from включительно: from = ts(e2) 12:00 — e2 входит, e1 (11:00) — нет
        $tester = $this->runCommand('app:billing:report', ['--from' => '2026-09-15T12:00:00Z']);
        self::assertSame(0, $tester->getStatusCode(), $tester->getDisplay());
        self::assertStringNotContainsString('u-alice,prism/lumen-8', $tester->getDisplay());
        self::assertStringContainsString('u-bob,prism/lumen-8', $tester->getDisplay());
    }

    public function testUnpricedEventWaitsForPriceThenIsRated(): void
    {
        // Модель, у которой пока нет ни одной строки цен: rate не падает,
        // а событие остаётся unrated и дожидается появления цен.
        $this->runCommand('app:prices:import', ['file' => self::PRICES]);

        $refFile = tempnam(sys_get_temp_dir(), 'ref-');
        $ref = [
            'providers' => [['code' => 'zzz', 'name' => 'ZZZ', 'currency' => 'USD']],
            'models' => [['provider' => 'zzz', 'code' => 'noPrice', 'description' => 'x']],
            'prices' => [],
        ];
        file_put_contents($refFile, json_encode($ref, JSON_UNESCAPED_UNICODE));
        $refTester = $this->runCommand('app:prices:import', ['file' => $refFile]);
        self::assertSame(0, $refTester->getStatusCode(), $refTester->getDisplay());

        $usageFile = tempnam(sys_get_temp_dir(), 'usage-');
        $noPriceLine = json_encode([
            'provider' => 'zzz',
            'model' => 'noPrice',
            'provider_request_id' => 'e5-noprice',
            'ts' => '2026-09-30T00:00:00Z',
            'user' => 'u-dave',
            'tokens_input' => 1000000,
            'tokens_output' => 1000000,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
        ], JSON_UNESCAPED_UNICODE);
        file_put_contents($usageFile, $noPriceLine . "\n");

        $tester = $this->runCommand('app:usage:import', ['file' => $usageFile]);
        self::assertSame(0, $tester->getStatusCode(), $tester->getDisplay());

        // 1-й pass: цены ещё нет.
        $tester = $this->runCommand('app:billing:rate');
        self::assertSame(0, $tester->getStatusCode(), $tester->getDisplay());
        self::assertSame('RATED=0 UNRATED=1', $this->lines($tester->getDisplay())[0]);

        // Цены появились — повторный rate рассчитал их.
        $refFile2 = tempnam(sys_get_temp_dir(), 'ref2-');
        $ref2 = [
            'providers' => [['code' => 'zzz', 'name' => 'ZZZ', 'currency' => 'USD']],
            'models' => [['provider' => 'zzz', 'code' => 'noPrice', 'description' => 'x']],
            'prices' => [[
                'provider' => 'zzz',
                'model' => 'noPrice',
                'valid_from' => '2026-09-01T00:00:00Z',
                'currency' => 'USD',
                'unit' => 'per_1m_tokens_unified',
                'rates' => ['any' => '6'],
            ]],
        ];
        file_put_contents($refFile2, json_encode($ref2, JSON_UNESCAPED_UNICODE));
        $ref2Tester = $this->runCommand('app:prices:import', ['file' => $refFile2]);
        self::assertSame(0, $ref2Tester->getStatusCode(), $ref2Tester->getDisplay());

        $tester = $this->runCommand('app:billing:rate');
        self::assertSame(0, $tester->getStatusCode(), $tester->getDisplay());
        self::assertSame('RATED=1 UNRATED=0', $this->lines($tester->getDisplay())[0]);

        // 2 000 000 токенов * 6 за 1M = 12.000000
        $status = $this->em->createQuery('SELECT e.status, e.cost FROM App\Entity\UsageEvent e WHERE e.providerRequestId = :id')
            ->setParameter('id', 'e5-noprice')
            ->getOneOrNullResult();
        $statusValue = $status['status'] instanceof \App\Domain\EventStatus
            ? $status['status']->value
            : (string) $status['status'];
        self::assertSame('rated', $statusValue);
        self::assertSame('12.000000', Decimal::of((string) $status['cost'])->format(6));
    }

    public function testRepriceUpdatesCostsAndWritesCorrections(): void
    {
        $this->runCommand('app:prices:import', ['file' => self::PRICES]);
        $this->runCommand('app:usage:import', ['file' => self::USAGE]);
        $this->runCommand('app:billing:rate');

        // Снижаем входную ставку lumen-8: 3.0 → 2.0 (строка от 2026-08-01).
        $repriceFile = tempnam(sys_get_temp_dir(), 'reprice-');
        $reprice = [
            'providers' => [],
            'models' => [],
            'prices' => [[
                'provider' => 'prism',
                'model' => 'lumen-8',
                'valid_from' => '2026-08-01T00:00:00Z',
                'currency' => 'USD',
                'unit' => 'per_1m_tokens',
                'rates' => ['input' => '2.0', 'output' => '12', 'cache_read' => '0.3', 'cache_write' => '3.75'],
            ]],
        ];
        file_put_contents($repriceFile, json_encode($reprice, JSON_UNESCAPED_UNICODE));

        $tester = $this->runCommand('app:billing:reprice', ['file' => $repriceFile]);
        self::assertSame(0, $tester->getStatusCode(), $tester->getDisplay());

        // e1: delta = (2.0 - 3.0) * 1 200 000 / 1M = -1.2;
        // e2 живёт по строке от 2026-09-15 — не затронута.
        self::assertSame(
            ['REPRICED_EVENTS=1', 'REPRICE_DELTA_USD=-1.200000'],
            $this->lines($tester->getDisplay()),
            $tester->getDisplay(),
        );

        // В журнале поправок — ровно одно изменение (e1).
        $corrections = $this->em->createQuery('SELECT COUNT(c.id) FROM App\Entity\PriceCorrection c')->getSingleScalarResult();
        self::assertSame('1', (string) $corrections);

        $e1 = $this->em->createQuery('SELECT e.cost FROM App\Entity\UsageEvent e WHERE e.providerRequestId = :id')
            ->setParameter('id', 'e1')
            ->getOneOrNullResult();
        // 1.2M*2.0 + 0.8M*12 + 2.5M*0.3 + 83333*3.75/1M = 2.4+9.6+0.75+0.31249875 = 13.06249875 → 13.062499
        self::assertSame('13.062499', Decimal::of((string) $e1['cost'])->format(6));

        // e2 пересчитан по той же (09-15) строке — без изменений.
        $e2 = $this->em->createQuery('SELECT e.cost FROM App\Entity\UsageEvent e WHERE e.providerRequestId = :id')
            ->setParameter('id', 'e2')
            ->getOneOrNullResult();
        self::assertSame('11.000000', Decimal::of((string) $e2['cost'])->format(6));
    }
}
