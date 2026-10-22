<?php

declare(strict_types=1);

namespace App\UI\Console\Command;

use App\Application\Billing\RepriceService;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Apply retroactive price corrections and recalculate affected events',
)]
final class RepriceCommand extends AbstractCommand
{
    public function __construct(private readonly RepriceService $service)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the corrected price rows JSON file');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->service->repriceFile((string) $input->getArgument('file'));
        } catch (InvalidArgumentException $exception) {
            $this->writeError($output, $exception->getMessage());

            return self::FAILURE;
        }

        $output->writeln(sprintf('REPRICED_EVENTS=%d', $result->repricedEvents));
        $output->writeln(sprintf('REPRICE_DELTA_USD=%s', $result->delta->format(6)));

        return self::SUCCESS;
    }
}
