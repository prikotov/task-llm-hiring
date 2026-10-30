<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DbSetupCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:db:setup')
            ->setDescription('Create/update the database schema from Doctrine entity metadata');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $meta = $this->em->getMetadataFactory()->getAllMetadata();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->updateSchema($meta);

        $output->writeln('DB schema ensured.');
        return 0;
    }
}
