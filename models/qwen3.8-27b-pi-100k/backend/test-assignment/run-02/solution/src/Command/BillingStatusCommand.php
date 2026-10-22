<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:status',
    description: 'Счётчики событий по статусам: new, rated, unrated',
)]
final class BillingStatusCommand extends AbstractBillingCommand
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function doExecute(InputInterface $input, OutputInterface $errorOut): int
    {
        foreach ([UsageEvent::STATUS_NEW, UsageEvent::STATUS_RATED, UsageEvent::STATUS_UNRATED] as $status) {
            $qb = $this->em->createQueryBuilder();
            $count = (int) $qb
                ->select('COUNT(e.id)')
                ->from(UsageEvent::class, 'e')
                ->where('e.status = :status')
                ->setParameter('status', $status)
                ->getQuery()
                ->getSingleScalarResult();

            $this->emit($errorOut, sprintf('%s: %s=%d', $this->getName(), $status, $count));
        }

        return 0;
    }
}
