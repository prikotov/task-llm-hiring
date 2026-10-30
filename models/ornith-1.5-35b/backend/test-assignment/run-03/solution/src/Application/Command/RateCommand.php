<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\PricingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

/**
 * Тарификация всех ещё не тарифицированных событий.
 *
 * stdout: сводка одной строкой `RATED=<n> UNRATED=<m>`.
 * stderr: по строке на unrated-событие этого запуска.
 */
#[AsCommand(name: 'app:billing:rate', description: 'Тарифицирует ещё не тарифицированные события')]
final class RateCommand extends Command
{
    public function __construct(private PricingService $service)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $result = $this->service->rate();
        } catch (Throwable $e) {
            $io->getErrorStyle()->error($e->getMessage());

            return Command::FAILURE;
        }

        // каждый unrated-событие этого запуска — строка в stderr
        foreach ($this->service->getUnratedWarnings() as $line) {
            fwrite(STDERR, $line . "\n");
        }

        // сводка в stdout одной строкой
        fwrite(STDOUT, 'RATED=' . $result['rated'] . ' UNRATED=' . $result['unrated']);
        fflush(STDOUT);
        fflush(STDERR);

        return Command::SUCCESS;
    }
}
