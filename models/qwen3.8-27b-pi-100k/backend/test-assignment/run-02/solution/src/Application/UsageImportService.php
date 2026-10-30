<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Dto\UsageImportSummary;
use App\Entity\Model;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Shared\Exception\BadInputException;
use App\Support\JsonFile;
use App\Support\SpecReader;
use App\Support\StrictIsoUtc;
use Doctrine\ORM\EntityManagerInterface;

/**
 * app:usage:import — приём событий из JSONL.
 *
 * Дедупликация по (provider, provider_request_id): повторно доставленное
 * событие целиком игнорируется (никаких обновлений, никаких доначислений).
 * Код выхода 0, даже если все строки — дубли; некорректная строка — ошибка.
 */
final class UsageImportService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /** @throws BadInputException */
    public function import(string $path): UsageImportSummary
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new BadInputException("Не удалось прочитать файл: {$path}");
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new BadInputException("Не удалось открыть файл: {$path}");
        }

        try {
            $inserted = 0;
            $duplicates = 0;

            $this->em->getConnection()->transactional(function () use ($handle, &$inserted, &$duplicates): void {
                $lineNo = 0;
                while (($line = fgets($handle)) !== false) {
                    ++$lineNo;
                    $trimmed = trim($line);
                    if ($trimmed === '') {
                        continue; // пустые строки (в т.ч. финальный перевод строки) игнорируем
                    }

                    $data = json_decode($trimmed, true);
                    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data) || array_is_list($data)) {
                        throw new BadInputException(
                            sprintf('Строка %d: некорректная строка JSONL%s', $lineNo, json_last_error() !== JSON_ERROR_NONE ? ' ('.json_last_error_msg().')' : ''),
                        );
                    }

                    $imported = $this->importEvent($data, $lineNo);
                    if ($imported > 0) {
                        ++$inserted;
                    } else {
                        ++$duplicates;
                    }
                }

                // DBAL-транзакция сама по себе ORM-сущности не флешит.
                $this->em->flush();
            });
        } finally {
            fclose($handle);
        }

        return new UsageImportSummary($inserted, $duplicates);
    }

    /** @return int 1 — событие вставлено, 0 — дубликат, проигнорирован */
    private function importEvent(array $data, int $lineNo): int
    {
        $context = "строка {$lineNo}";

        $providerCode = SpecReader::requiredString($data, 'provider', $context, 128);
        $requestId = SpecReader::requiredString($data, 'provider_request_id', $context, 128);
        $modelCode = SpecReader::requiredString($data, 'model', $context, 128);
        $user = SpecReader::requiredString($data, 'user', $context, 255);
        $tsRaw = SpecReader::requiredString($data, 'ts', $context, 64);

        $tokens = [
            'input' => SpecReader::nonNegativeInt($data, 'tokens_input', $context),
            'output' => SpecReader::nonNegativeInt($data, 'tokens_output', $context),
            'cache_read' => SpecReader::nonNegativeInt($data, 'tokens_cache_read', $context),
            'cache_write' => SpecReader::nonNegativeInt($data, 'tokens_cache_write', $context),
        ];
        $ts = StrictIsoUtc::parse($tsRaw);

        $provider = $this->em
            ->getRepository(Provider::class)
            ->findOneBy(['code' => $providerCode]);
        if ($provider === null) {
            throw new BadInputException("{$context}.provider: провайдер «{$providerCode}» не найден в справочнике");
        }

        $model = $this->em
            ->getRepository(Model::class)
            ->findOneBy(['provider' => $provider, 'code' => $modelCode]);
        if ($model === null) {
            throw new BadInputException("{$context}: модель «{$providerCode}/{$modelCode}» не найдена в справочнике");
        }

        $existing = $this->em
            ->getRepository(UsageEvent::class)
            ->findOneBy(['provider' => $provider, 'providerRequestId' => $requestId]);

        if ($existing !== null) {
            // Дубликат: игнорируем целиком, независимо от остальных полей строки.
            return 0;
        }

        $this->em->persist(new UsageEvent($provider, $requestId, $model, $user, $ts, $tsRaw, $tokens));

        return 1;
    }
}
