<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\RateService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:rate',
    description: 'Рассчитать стоимость всех не рассчитанных событий (повторяет unrated)',
)]
final class BillingRateCommand extends AbstractBillingCommand
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function doExecute(InputInterface $input, OutputInterface $errorOut): int
    {
        $summary = (new RateService($this->em))->rate();

        // Сводка — одна строка строго в stdout.
        fwrite(STDOUT, sprintf('RATED=%d UNRATED=%d', $summary->rated, count($summary->unrated))."\n");

        // Для каждого unrated-события этого запуска — строка в stderr.
        foreach ($summary->unrated as $event) {
            $errorOut->writeln(sprintf(
                'UNRATED %s/%s %s no_price',
                $event->getProvider()->getCode(),
                $event->getProviderRequestId(),
                $event->getTsRaw(),
            ));
        }

        return 0;
    }
}
