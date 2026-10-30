<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\RepriceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:reprice',
    description: 'Ретро-коррекция цены: применить исправленные строки цен, пересчитать события, записать журнал',
)]
final class BillingRepriceCommand extends AbstractBillingCommand
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'JSON-файл коррекции того же формата, что история цен (используется секция prices)')
            ->setHelp(
                'Применяет исправленные строки цен (upsert по (provider, model, valid_from); '
                .'провайдер и модель обязаны существовать в базе), пересчитывает '
                .'rated-события по заменённым строкам и пишет журнал корректировок. '
                .'В stdout ровно две строки: REPRICED_EVENTS=<n> и REPRICE_DELTA_USD=<x.xxxxxx>.',
            );
    }

    protected function doExecute(InputInterface $input, OutputInterface $errorOut): int
    {
        /** @var string $path */
        $path = $input->getArgument('file');

        $summary = (new RepriceService($this->em))->apply($path);

        // Вывод — строго две строки в stdout (служебные выводы — только при ошибке).
        fwrite(STDOUT, sprintf(
            "REPRICED_EVENTS=%d\nREPRICE_DELTA_USD=%s\n",
            $summary->eventsRepriced,
            $summary->deltaUsd,
        ));

        return 0;
    }
}
