<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\Repricer;
use App\Application\Exception\FileFormatException;
use App\Application\Pricing\PriceFileParser;
use App\Domain\Common\Decimal;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Применяет исправленные строки цен и пересчитывает события по ним (бонус)',
)]
final class BillingRepriceCommand extends Command
{
    public function __construct(private readonly Repricer $repricer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с исправленными строками цен')
            ->setHelp(
                'Документ коррекции — в том же формате, что app:prices:import (секции providers/models '.
                'могут отсутствовать). Пересчитываются события, тарифицированные указанными строками цен; '.
                'суммы в итоговом отчёте заменяются, без добавления новых событий.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument('file');
        $stderr = $output->getErrorOutput();

        $content = PricesImportCommand::readFile($path, $stderr);
        if ($content === null) {
            return Command::FAILURE;
        }

        try {
            $counts = $this->repricer->reprice(PriceFileParser::fromJson($content));
        } catch (FileFormatException $e) {
            $stderr->writeln('Ошибка формата данных: '.$e->getMessage());

            return Command::FAILURE;
        } catch (Throwable $e) {
            $stderr->writeln('Ошибка пересчёта: '.$e->getMessage());

            return Command::FAILURE;
        }

        // stdout — ровно две сводные строки.
        $output->writeln(sprintf('REPRICED_EVENTS=%d', $counts['events']));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $counts['delta']->format(Decimal::MONEY_SCALE)));

        $stderr->writeln(sprintf(
            'Пересчитано событий: %d, дельта: %s %s.',
            $counts['events'],
            $counts['delta']->format(Decimal::MONEY_SCALE),
            'USD',
        ));

        return Command::SUCCESS;
    }
}
