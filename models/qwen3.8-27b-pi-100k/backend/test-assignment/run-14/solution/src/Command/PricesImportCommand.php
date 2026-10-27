<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\PricesImportService;
use App\Exception\InvalidInputException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:prices:import',
    description: 'Load the pricing reference (providers, models, price rows).',
)]
final class PricesImportCommand extends Command
{
    public function __construct(
        private PricesImportService $svc,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to prices.json (ISO-8601 UTC for valid_from).');
    }

    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $style = new SymfonyStyle($in, $out);
        $file = (string) $in->getArgument('file');
        try {
            $this->svc->importFile($file);
        } catch (InvalidInputException $e) {
            $style->error('prices import failed: ' . $e->getMessage());
            return 1;
        } catch (\Throwable $e) {
            $style->error('prices import failed: ' . $e->getMessage());
            return 1;
        }

        $style->success('Pricing reference imported.');
        return self::SUCCESS;
    }
}
