<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Common\RepriceException;
use App\Domain\Common\Input;
use App\Domain\Common\ImportFormatException;
use App\Domain\Pricing\RepriceLine;
use App\Domain\Pricing\RepriceService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function json_decode;

/**
 * app:billing:reprice <file> — ретро-коррекция цены (бонус).
 *
 * Файл коррекции — тот же формат, что справочник цен; секции
 * providers/models в нём могут отсутствовать или быть пустыми.
 */
#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Применить исправленные строки цен и пересчитать затронутые события',
)]
final class BillingRepriceCommand extends Command
{
    public function __construct(private readonly RepriceService $reprice)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Путь к JSON-файлу с исправленными строками цен');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');

        $real = realpath($file);
        if ($real === false) {
            $io->error('Файл не найден: '.$file);

            return Command::FAILURE;
        }

        $lines = $this->parse($real, $io);
        if ($lines === false) {
            return Command::FAILURE;
        }

        try {
            $result = $this->reprice->reprice($lines);
        } catch (RepriceException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $fmtDelta = \App\Domain\Common\Money::format($result['delta']);
        $output->writeln('REPRICED_EVENTS='.$result['repriced']);
        $output->writeln('REPRICE_DELTA_USD='.$fmtDelta);

        return Command::SUCCESS;
    }

    /** @return list<RepriceLine>|false */
    private function parse(string $path, SymfonyStyle $io): array|false
    {
        $raw = file_get_contents($path);
        if ($raw === false) {
            $io->error('Не удалось прочитать файл: '.$path);

            return false;
        }

        try {
            $json = json_decode($raw, true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $io->error('JSON: '.$e->getMessage());

            return false;
        }

        if (!\is_array($json)) {
            $io->error('JSON: корневой элемент должен быть объектом');

            return false;
        }

        $prices = $json['prices'] ?? [];
        if (!\is_array($prices)) {
            $io->error('JSON: секция prices должна быть массивом');

            return false;
        }

        $lines = [];
        foreach ($prices as $i => $row) {
            try {
                $row = Input::asRow($row, "{$path}: prices[{$i}]");
                $lines[] = RepriceLine::fromArray($row, "{$path}: prices[{$i}]");
            } catch (ImportFormatException $e) {
                $io->error($e->getMessage());

                return false;
            }
        }

        return $lines;
    }
}
