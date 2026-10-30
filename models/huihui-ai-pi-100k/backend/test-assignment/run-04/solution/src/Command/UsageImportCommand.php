<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\BillingStore;
use App\Ingest\IngestError;
use App\Ingest\PriceFileParser;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:usage:import', description: 'Загружает события использования (JSONL); дубли по (provider, provider_request_id) игнорируются')]
final class UsageImportCommand extends Command
{
    public function __construct(
        private readonly PriceFileParser $parser,
        private readonly BillingStore $store,
        private readonly Connection $db,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'JSONL-файл со событиями (одно событие на строку)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = (string) $input->getArgument('file');

        try {
            $content = FileSupport::read($file);
        } catch (\RuntimeException $e) {
            return CommandSupport::fail($output, $e->getMessage());
        }

        $events = [];
        $lines = explode("\n", str_replace("\r\n", "\n", $content));
        foreach ($lines as $i => $line) {
            if (trim($line) === '') {
                continue; // пустые строки (в т.ч. завершающий перевод строки) игнорируются
            }

            try {
                $events[] = $this->parser->parseUsageLine($line);
            } catch (IngestError $e) {
                return CommandSupport::fail($output, sprintf('строка %d: %s', $i + 1, $e->getMessage()));
            }
        }

        try {
            $this->db->transactional(function () use ($events): void {
                foreach ($events as $event) {
                    $this->store->persistImported($event);
                }
            });
        } catch (\Throwable $e) {
            return CommandSupport::fail($output, $e->getMessage());
        }

        return 0;
    }
}
