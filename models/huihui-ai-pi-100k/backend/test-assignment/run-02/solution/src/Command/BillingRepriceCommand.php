<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Money;
use App\Import\PriceFileParser;
use App\Service\RepriceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Ретро-коррекция цены (бонус): файл с новыми значениями строки цен
 * (формат как секция prices файла импорта).
 *
 * Затронутые строки обновляются (создаются, если их ещё не было), уже
 * рассчитанные по ним события пересчитываются на месте (стоимость
 * перезаписывается — дублирования в отчёте нет), каждая корректировка
 * фиксируется в журнале (таблица reprice_log).
 *
 * Машинные строки (stdout):
 *   REPRICED_EVENTS=<n>
 *   REPRICE_DELTA_USD=<x.xxxxxx>
 * остальные сообщения — stderr.
 */
#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция цены: пересчёт уже рассчитанных событий по новым ставкам',
)]
final class BillingRepriceCommand extends Command
{
    public function __construct(
        private readonly RepriceService $repriceService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::OPTIONAL,
                'Файл коррекции (JSON, формат секции «prices»); «-» — из stdin')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED,
                'Файл коррекции (JSON, формат секции «prices»); «-» — из stdin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) ($input->getArgument('file') ?: $input->getOption('file'));
        if ($file === '') {
            $output->getErrorOutput()->writeln('Ошибка: нужен параметр --file (или «-» для stdin).');

            return Command::FAILURE;
        }

        try {
            $rows = PriceFileParser::parseFile($file)->prices;
        } catch (\Exception $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        if ($rows === []) {
            $output->getErrorOutput()->writeln('Коррекция: секция prices пуста — делать нечего.');
            $output->writeln('REPRICED_EVENTS=0');
            $output->writeln('REPRICE_DELTA_USD=0.000000');

            return Command::SUCCESS;
        }

        try {
            $result = $this->repriceService->apply($rows);
        } catch (\Doctrine\DBAL\Exception $e) {
            $output->getErrorOutput()->writeln('Ошибка (БД): ' . $e->getMessage());

            return Command::FAILURE;
        } catch (\RuntimeException|\ValueError|\InvalidArgumentException $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $errorOutput = $output->getErrorOutput();
        $errorOutput->writeln('Коррекция применена.');

        $output->writeln('REPRICED_EVENTS=' . $result['events']);
        $output->writeln('REPRICE_DELTA_USD=' . $this->formatDelta($result['delta']));

        return Command::SUCCESS;
    }

    /**
     * Дельта — точное значение с шестью знаками (могут быть отрицательной);
     * промежуточные суммы считаются bcmath, float не участвует.
     */
    private function formatDelta(Money $delta): string
    {
        try {
            return $delta->format(Money::US_DECIMALS);
        } catch (\ValueError) {
            return '0.000000';
        }
    }
}
