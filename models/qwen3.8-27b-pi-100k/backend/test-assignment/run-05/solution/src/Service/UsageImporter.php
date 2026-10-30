<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\UsageEvent;
use App\Shared\ImportException;
use App\Shared\InputValidator;
use Doctrine\ORM\EntityManagerInterface;

final class UsageImporter
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function import(string $path): void
    {
        if (!is_readable($path)) {
            throw new ImportException(sprintf('usage: файл "%s" недоступен для чтения', $path));
        }

        $this->em->wrapInTransaction(function () use ($path): void {
            $lineNo = 0;
            $handle = fopen($path, 'rb');
            if ($handle === false) {
                throw new ImportException(sprintf('usage: не удалось открыть файл "%s"', $path));
            }

            try {
                while (($line = fgets($handle)) !== false) {
                    $lineNo++;
                    if (trim($line) === '') {
                        continue; // пустые строки пропускаем
                    }
                    $this->importLine($line, $lineNo);
                }
            } finally {
                fclose($handle);
            }
            $this->em->flush();
        });
    }

    private function importLine(string $line, int $lineNo): void
    {
        $ctx = sprintf('usage: строка %d', $lineNo);
        try {
            $data = json_decode(trim($line), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException(sprintf('%s: некорректный JSON: %s', $ctx, $e->getMessage()));
        }
        if (!is_array($data)) {
            throw new ImportException(sprintf('%s: событие должно быть JSON-объектом', $ctx));
        }

        $provider = InputValidator::requireString($data, 'provider', $ctx);
        $requestId = InputValidator::requireString($data, 'provider_request_id', $ctx);
        $model = InputValidator::requireString($data, 'model', $ctx);
        $user = InputValidator::requireString($data, 'user', $ctx);
        $ts = InputValidator::utcDateTime(InputValidator::requireString($data, 'ts', $ctx), $ctx);
        $tokensInput = InputValidator::nonNegativeInt($data['tokens_input'] ?? null, 'tokens_input', $ctx);
        $tokensOutput = InputValidator::nonNegativeInt($data['tokens_output'] ?? null, 'tokens_output', $ctx);
        $tokensCacheRead = InputValidator::nonNegativeInt($data['tokens_cache_read'] ?? null, 'tokens_cache_read', $ctx);
        $tokensCacheWrite = InputValidator::nonNegativeInt($data['tokens_cache_write'] ?? null, 'tokens_cache_write', $ctx);

        $existing = $this->em->getRepository(UsageEvent::class)->findOneBy([
            'provider' => $provider,
            'providerRequestId' => $requestId,
        ]);
        if ($existing !== null) {
            return; // дубль: игнорируем целиком, без обновлений
        }

        $this->em->persist(new UsageEvent(
            $provider,
            $requestId,
            $model,
            $user,
            $ts,
            $tokensInput,
            $tokensOutput,
            $tokensCacheRead,
            $tokensCacheWrite,
        ));
    }
}
