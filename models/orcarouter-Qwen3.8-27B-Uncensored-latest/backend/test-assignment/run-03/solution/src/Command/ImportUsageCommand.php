<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\UsageEventService;
use App\Support\Input;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import')]
final class ImportUsageCommand extends AppCommand
{
    public function __construct(private readonly UsageEventService $events)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Загрузить события использования из JSONL (идемпотентно)')
            ->addArgument('file', InputArgument::REQUIRED, 'путь к JSONL-файлу с событиями');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $rows = Input::loadJsonl($file);
            $result = $this->events->import($rows);
        } catch (\RuntimeException | \JsonException $e) {
            $this->error($output, 'app:usage:import: ' . $e->getMessage());

            return self::FAILURE;
        }

        // Служебная информация — в stderr; даже при всех дублях код выхода 0.
        $this->note($output, sprintf(
            'imported events: new=%d duplicate=%d',
            $result['new'],
            $result['duplicate'],
        ));

        return self::SUCCESS;
    }
}
