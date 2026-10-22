<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RatingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:rate')]
final class RateCommand extends AppCommand
{
    public function __construct(private readonly RatingService $rating)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Тарифицировать ещё не рассчитанные события');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->rating->rateAll();
        } catch (\Throwable $e) {
            $this->error($output, 'app:billing:rate: ' . $e->getMessage());

            return self::FAILURE;
        }

        foreach ($result['unratedLines'] as $line) {
            $this->note($output, $line);
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], $result['unrated']));

        return self::SUCCESS;
    }
}
