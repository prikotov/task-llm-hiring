<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\RepriceService;
use App\Domain\Billing\Money;
use App\Domain\Exception\ImportException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Ретро-коррекция ставок:
 *   bin/console app:billing:reprice fixtures/prices-corrected.json
 *
 * Файл — в формате файла цен (upsert по (provider, model, valid_from));
 * секции providers/models могут отсутствовать или быть пустыми.
 * В stdout ровно две строки:
 *   REPRICED_EVENTS=<n>
 *   REPRICE_DELTA_USD=<x.xxxxxx>
 */
#[AsCommand(name: 'app:billing:reprice', description: 'Ретро-коррекция строк цен и пересчёт тарифицированных по ним событий')]
final class AppBillingRepriceCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с исправленными строками цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');
        if (!is_file($file) || !is_readable($file)) {
            $output->getErrorOutput()->writeln(sprintf('Не удалось прочитать файл "%s".', $file));

            return Command::FAILURE;
        }

        try {
            $result = (new RepriceService($this->em))->reprice((string) file_get_contents($file));
        } catch (ImportException $e) {
            $output->getErrorOutput()->writeln('Не удалось применить коррекцию: '.$e->getMessage());

            return Command::FAILURE;
        } catch (\Throwable $e) {
            $output->getErrorOutput()->writeln('Не удалось применить коррекцию: '.$e->getMessage());

            return Command::FAILURE;
        }

        $output->writeln('REPRICED_EVENTS='.(int) $result['events']);
        $output->writeln('REPRICE_DELTA_USD='.Money::format6($result['delta']));

        return Command::SUCCESS;
    }
}
