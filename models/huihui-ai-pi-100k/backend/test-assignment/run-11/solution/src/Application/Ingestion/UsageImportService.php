<?php

declare(strict_types=1);

namespace App\Application\Ingestion;

use App\Domain\Usage\UsageEvent;
use App\Domain\Usage\UsageEventEntity;
use App\Domain\Usage\RatedEventEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Идемпотентная загрузка событий использования из JSONL-файла.
 *
 * Дедупликация — по (provider, provider_request_id):
 *  - если событие уже импортировано  — игнорируется целиком;
 *  - если событие уже рассчитано     — тоже игнорируется (обратного
 *                                     хода нет: расходы уже показаны).
 *
 * Вывод: в stderr количество импортированных/проигнорированных строк.
 */
final class UsageImportService
{
    /**
     * @return array{inserted:int, ignored:int}
     */
    public function import(string $jsonlFile, EntityManagerInterface $em): array
    {
        $parser = new UsageEventsParser();
        try {
            $file = file_get_contents($jsonlFile);
            if ($file === false) {
                throw new \App\Domain\DomainException("file not readable: {$jsonlFile}");
            }
            $lines = array_values(array_filter(array_map('trim', explode("\n", $file))));
            $events = $parser->parse($lines);
        } catch (\App\Domain\DomainException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw \App\Domain\DomainException::wrap($e, "usage import ({$jsonlFile})");
        }

        $inserted = 0;
        $ignored = 0;

        foreach ($events as $event) {
            $exists = $em->getRepository(UsageEventEntity::class)->findOneBy(
                ['provider' => $event->provider, 'providerRequestId' => $event->providerRequestId]
            );
            if ($exists === null) {
                $entity = new UsageEventEntity();
                $entity->provider = $event->provider;
                $entity->providerRequestId = $event->providerRequestId;
                $entity->model = $event->model;
                $entity->user = $event->user;
                $entity->ts = $event->ts;
                $entity->tokensInput = $event->tokens->input;
                $entity->tokensOutput = $event->tokens->output;
                $entity->tokensCacheRead = $event->tokens->cacheRead;
                $entity->tokensCacheWrite = $event->tokens->cacheWrite;
                $em->persist($entity);
                $inserted++;
            } else {
                $ignored++;
            }
        }

        $em->flush();

        return ['inserted' => $inserted, 'ignored' => $ignored];
    }
}
