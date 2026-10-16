<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\DomainException;
use App\Entity\UsageEvent;
use App\Ingest\UsageEventInput;
use App\Ingest\UsageImportResult;
use App\Ingest\UsageLineParser;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Приём журнала использования с идемпотентностью по (provider, provider_request_id).
 */
final class UsageImportService
{
    public function __construct(
        private readonly UsageLineParser $parser,
        private readonly CatalogService $catalog,
    ) {
    }

    /**
     * @throws DomainException при некорректной строке или неизвестном справочнике
     */
    public function importFile(EntityManagerInterface $em, string $path): UsageImportResult
    {
        if (!is_file($path)) {
            throw new DomainException(sprintf('Файл не найден: %s', $path));
        }

        $lines = file($path, \FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new DomainException(sprintf('Не удалось прочитать файл: %s', $path));
        }

        // Весь файл — в одной транзакции: ошибка на любой строке откатывает импорт,
        // частичных состояний нет.
        $em->beginTransaction();
        try {
            $duplicates = 0;
            $imported = 0;
            $lineNumber = 0;

            foreach ($lines as $line) {
                $lineNumber++;
                $event = $this->parser->parse($line, $lineNumber + 1);
                if ($this->insertIfAbsent($em, $event)) {
                    $imported++;
                } else {
                    $duplicates++;
                }
            }

            $em->commit();
        } catch (\Throwable $e) {
            $em->rollback();
            $em->clear(UsageEvent::class);
            $em->clear();

            throw $e;
        }

        return new UsageImportResult($imported, $duplicates);
    }

    /**
     * @throws DomainException
     */
    private function insertIfAbsent(EntityManagerInterface $em, UsageEventInput $event): bool
    {
        $provider = $this->catalog->providerByCode($em, $event->provider);
        if ($provider === null) {
            throw new DomainException(sprintf('Неизвестный провайдер "%s" (импортируйте цены/справочник ранее)', $event->provider));
        }

        $model = $this->catalog->modelByCode($em, $provider->id, $event->model);
        if ($model === null) {
            throw new DomainException(sprintf('Неизвестная модель %s/%s (импортируйте цены/справочник ранее)', $event->provider, $event->model));
        }

        $existing = $em->createQuery(
            'SELECT e.id FROM '.UsageEvent::class.' e JOIN e.provider p WHERE p.id = :pid AND e.providerRequestId = :rid'
        )
            ->setParameter('pid', $provider->id)
            ->setParameter('rid', $event->providerRequestId)
            ->getOneOrNullResult();

        if ($existing !== null) {
            return false;
        }

        $entity = new UsageEvent();
        $entity->provider = $provider;
        $entity->providerRequestId = $event->providerRequestId;
        $entity->model = $model;
        $entity->user = $event->user;
        $entity->occurredAt = $event->occurredAt;
        $entity->tokensInput = $event->tokensInput;
        $entity->tokensOutput = $event->tokensOutput;
        $entity->tokensCacheRead = $event->tokensCacheRead;
        $entity->tokensCacheWrite = $event->tokensCacheWrite;
        $em->persist($entity);

        try {
            // единичный flush, чтобы нарушение уникального индекса (гоночные дубли)
            // не тянуло весь импорт за собой
            $em->flush();
        } catch (UniqueConstraintViolationException) {
            $em->clear(UsageEvent::class);

            return false;
        }

        return true;
    }
}
