<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RateRunner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Расчёт стоимости нерассчитанных событий',
)]
final class RateCommand extends AppCommand
{
    public function __construct(private RateRunner $runner)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->runner->rate();
        } catch (\Throwable $e) {
            return $this->fail($output, $e);
        }

        foreach ($result['unrated'] as $u) {
            $this->err($output, sprintf('UNRATED %s/%s %s no_price', $u['provider'], $u['request'], $u['ts']));
        }

        $output->writeln(sprintf('RATED=%d UNRATED=%d', $result['rated'], \count($result['unrated'])));

        return self::SUCCESS;
    }
}
