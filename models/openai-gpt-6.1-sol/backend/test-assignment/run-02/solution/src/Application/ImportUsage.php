<?php

declare(strict_types=1);

namespace App\Application;

use App\Infrastructure\Input;
use App\Infrastructure\WriteTransaction;
use Doctrine\DBAL\Connection;

final class ImportUsage
{
    public function __construct(private Input $input, private Connection $connection, private WriteTransaction $transaction)
    {
    }

    public function run(string $file): void
    {
        $this->transaction->run(function () use ($file): void {
            foreach ($this->input->usage($file) as $event) {
                // DO NOTHING принципиален: даже изменившееся содержимое дубля не обновляет событие.
                $this->connection->executeStatement(
                    <<<'SQL'
                    INSERT INTO billing_usage
                        (provider_code, provider_request_id, model_code, user_id, occurred_at,
                         tokens_input, tokens_output, tokens_cache_read, tokens_cache_write)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON CONFLICT (provider_code, provider_request_id) DO NOTHING
                    SQL,
                    [
                        $event->providerCode, $event->providerRequestId, $event->modelCode, $event->userId,
                        $event->occurredAt->format('Y-m-d H:i:s.u'),
                        $event->tokens->input, $event->tokens->output, $event->tokens->cache_read, $event->tokens->cache_write,
                    ],
                );
            }
        });
    }
}
