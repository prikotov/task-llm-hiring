<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\UsageImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:usage:import',
    description: 'Принять события использования из JSONL (дедупликация по provider_request_id)',
)]
final class UsageImportCommand extends AbstractBillingCommand
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл с событиями (по одному объекту на строку)')
            ->setHelp('Принимает события. Повторно доставленные строки (provider, provider_request_id) игнорируются. Вывод: inserted=, duplicates=.');
    }

    protected function doExecute(InputInterface $input, OutputInterface $errorOut): int
    {
        /** @var string $path */
        $path = $input->getArgument('file');

        $summary = (new UsageImportService($this->em))->import($path);

        $this->emit(
            $errorOut,
            sprintf(
                '%s: OK inserted=%d duplicates=%d',
                $this->getName(),
                $summary->inserted,
                $summary->duplicates,
            ),
        );

        return 0;
    }
}
