<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Billing\IsoDateTime;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Загрузка событий использования (JSONL) по правилу идемпотентности:
 * дедупликация по (provider, provider_request_id).
 */
final class UsageImporter
{
    public function __construct(
        private readonly BillingRepository $repo,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * Возвращает [импортировано, пропущено как дубли].
     *
     * @return array{int, int}
     */
    public function import(string $content): array
    {
        $imported = 0;
        $skipped = 0;

        $lines = preg_split('/\R/u', $content) ?: [];
        $lineNo = 0;

        foreach ($lines as $line) {
            $lineNo++;
            $trimmed = trim($line);
            if ('' === $trimmed) {
                continue;
            }

            $raw = json_decode($trimmed, true, 128, \JSON_THROW_ON_ERROR);

            if (!\is_array($raw)) {
                throw new \RuntimeException(sprintf('Строка %d: ожидается JSON-объект', $lineNo));
            }

            $result = $this->process($raw);

            if ($result['duplicate']) {
                $skipped++;
            } else {
                $imported++;
            }
        }

        $this->em->flush();

        return [$imported, $skipped];
    }

    /**
     * Валидация -> дедупликация -> сохранение одного события.
     *
     * @param array<string, mixed> $raw
     *
     * @return array{duplicate: bool}
     */
    private function process(array $raw): array
    {
        // 1) Собираем обязательные поля (валидация типов).
        $providerCode = Validators::string($raw, 'provider');
        $providerRequestId = Validators::string($raw, 'provider_request_id');
        $modelCode = Validators::string($raw, 'model');
        $user = Validators::string($raw, 'user');
        $ts = IsoDateTime::parseUtc(Validators::string($raw, 'ts'));
        $tokensInput = self::tokens($raw, 'tokens_input');
        $tokensOutput = self::tokens($raw, 'tokens_output');
        $tokensCacheRead = self::tokens($raw, 'tokens_cache_read');
        $tokensCacheWrite = self::tokens($raw, 'tokens_cache_write');

        // 2) Дедупликация по (provider, provider_request_id).
        if (null !== $this->repo->findEvent($providerCode, $providerRequestId)) {
            return ['duplicate' => true];
        }

        // 3) Ссылки на сущности должны существовать (provider и model из базы).
        $provider = $this->repo->findProviderByCode($providerCode);
        if (null === $provider) {
            throw new \DomainException(sprintf('Неизвестный провайдер: "%s"', $providerCode));
        }

        $model = $this->repo->findModelByProviderAndCode($provider, $modelCode);
        if (null === $model) {
            throw new \DomainException(sprintf('Неизвестная модель: "%s/%s"', $providerCode, $modelCode));
        }

        // 4) Сохраняем новое событие.
        $event = new UsageEvent();
        $event->setProvider($provider)
            ->setProviderRequestId($providerRequestId)
            ->setModel($model)
            ->setUser($user)
            ->setTs($ts)
            ->setTokensInput($tokensInput)
            ->setTokensOutput($tokensOutput)
            ->setTokensCacheRead($tokensCacheRead)
            ->setTokensCacheWrite($tokensCacheWrite);

        $this->em->persist($event);

        return ['duplicate' => false];
    }

    /**
     * @param array<string, mixed> $raw
     */
    private static function tokens(array $raw, string $key): int
    {
        if (!\array_key_exists($key, $raw)) {
            return 0;
        }

        $value = $raw[$key];
        if (!\is_int($value) || $value < 0) {
            throw new \DomainException(sprintf('%s должен быть целым >= 0', $key));
        }

        return $value;
    }
}
