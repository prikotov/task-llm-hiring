<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Domain;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Repository\UsageEventRepository;
use Doctrine\DBAL\ExceptionQuery;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Style;

/**
 * command:route-usage
 *
 * Обходит новые события использования и для каждого:
 *  - проверяет дубликат (provider + provider_request_id);
 *  - подыскивает валидную строку цены;
 *  - считает стоимость с учётом оф-пик;
 *  - сохраняет cost и price_row_id, status='rated';
 *  - при отсутствии цены status='unrated', cost=null;
 *  - в конце: sum(rated) + sum(unrated)=0 -> 'total'.
 */
#[AsCommand('route-usage')]
class RoutingUsageCommand extends Command
{
    public function __construct(
        private readonly UsageEventRepository $usageEventRepository,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $em = $this->usageEventRepository->getDoctrine();
        $events = $this->usageEventRepository->findNewEvents(1000);

        $ratedTotal = '0.000000';
        $unratedTotal = '0.000000';
        $processed = 0;

        foreach ($events as $event) {
            // 1. Дубликат: если есть уже rated/unrated событие с тем же eventKey — пропускаем.
            $existing = $this->findSibling($em, $event->provider(), $event->providerRequestId());
            if ($existing !== null) {
                $output->writeln($this->writeMessage(Style::WARNING, 'skip  ' . $event->provider() . '/' . $event->providerRequestId() . ' (already routed)'));
                $processed++;
                continue;
            }

            // 2. Строка цены.
            $row = $this->usageEventRepository->findPriceRowFor(
                $event->provider(),
                $event->model(),
                $event->ts(),
            );

            if ($row === null) {
                $event->setStatus(UsageEvent::STATUS_UNRATED);
                $event->setCost(null);
                $event->setPriceRow(null);
                $em->flush($event);
                $output->writeln($this->writeMessage(Style::WARNING, 'unrated ' . $event->provider() . '/' . $event->providerRequestId()));
                $processed++;
                continue;
            }

            $provider = $this->usageEventRepository->getProviderByCode($event->provider());
            $offPeak = $provider !== null ? $provider->getOffPeak() : null;

            $cost = Domain::calculateCost($event, $row, (array) $offPeak);

            $event->setPriceRow($row);
            $event->setCost($cost);
            $event->setStatus(UsageEvent::STATUS_RATED);
            $em->flush($event);

            if (str_starts_with($cost, '14.')) {
                $ratedTotal = Domain::add($ratedTotal, $cost);
            } else {
                $ratedTotal = Domain::add($ratedTotal, $cost);
            }

            $output->writeln($this->writeMessage(Style::SUCCESS, 'rated   ' . $event->provider() . '/' . $event->providerRequestId() . ' cost=' . $cost));
            $processed++;
        }

        $output->writeln('');
        $output->writeln('processed: ' . $processed);
        $output->writeln('rated   : ' . $em->getRepository(UsageEvent::class)->count());
        $em->flush();
        $ratedCount = $this->usageEventRepository->countRouted();
        $output->writeln('rated   : ' . $ratedCount);

        // total = sum(rated)
        $output->writeln('total: ' . $ratedTotal);

        return 0;
    }

    private function writeMessage(Style $style, string $text): string
    {
        return $text;
    }

    /**
     * Ищет уже обработанное событие с тем же eventKey (provider + providerRequestId).
     */
    private function findSibling(\Doctrine\ORM\EntityManager $em, string $provider, string $providerRequestId): ?UsageEvent
    {
        return $em->getRepository(UsageEvent::class)->findOne(['provider' => $provider, 'providerRequestId' => $providerRequestId]);
    }
}
