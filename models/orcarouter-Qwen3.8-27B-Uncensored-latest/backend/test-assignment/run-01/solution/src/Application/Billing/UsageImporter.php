<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Billing\Entity\UsageEvent;
use App\Domain\Billing\Exception\BillingException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Импорт JSONL-событий использования. Дедупликация по
 * (provider, provider_request_id): уже известные события игнорируются
 * целиком — обновлений и доначислений нет.
 */
final class UsageImporter
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @return int количество реально сохранённых (новых) событий
     *
     * @throws BillingException при некорректной строке
     */
    public function import(string $path): int
    {
        if (!\is_file($path)) {
            throw new BillingException(sprintf('Файл не найден: %s', $path));
        }

        $fh = \fopen($path, 'r');
        if ($fh === false) {
            throw new BillingException(sprintf('Не удалось открыть файл: %s', $path));
        }

        $accepted = 0;
        $line = 0;
        try {
            while (($raw = \fgets($fh)) !== false) {
                $line++;
                $raw = \trim($raw);
                if ($raw === '') {
                    continue;
                }

                $data = \json_decode($raw, true);
                if (\json_last_error() !== \JSON_ERROR_NONE || !\is_array($data)) {
                    throw new BillingException(sprintf('Строка %d: некорректный JSON.', $line));
                }

                try {
                    if ($this->persistEvent((array) $data)) {
                        $accepted++;
                    }
                } catch (BillingException $e) {
                    throw new BillingException(sprintf('Строка %d: %s', $line, $e->getMessage()));
                }
            }
        } finally {
            \fclose($fh);
        }

        $this->em->flush();

        return $accepted;
    }

    /**
     * Возвращает true, если событие сохранено, false — если уже существовало.
     *
     * @param array<string, mixed> $data
     */
    private function persistEvent(array $data): bool
    {
        $provider = $this->nonEmptyString($data['provider'] ?? null, 'provider');
        $requestId = $this->nonEmptyString($data['provider_request_id'] ?? null, 'provider_request_id');
        $model = $this->nonEmptyString($data['model'] ?? null, 'model');
        $user = $this->nonEmptyString($data['user'] ?? null, 'user');
        $ts = $this->parseTimestamp($data['ts'] ?? null, 'ts');

        $event = new UsageEvent(
            $provider,
            $requestId,
            $model,
            $user,
            $ts,
            $this->nonNegativeInt($data['tokens_input'] ?? 0, 'tokens_input'),
            $this->nonNegativeInt($data['tokens_output'] ?? 0, 'tokens_output'),
            $this->nonNegativeInt($data['tokens_cache_read'] ?? 0, 'tokens_cache_read'),
            $this->nonNegativeInt($data['tokens_cache_write'] ?? 0, 'tokens_cache_write')
        );

        $existing = $this->em->getRepository(UsageEvent::class)->findOneBy([
            'provider' => $provider,
            'requestId' => $requestId,
        ]);

        if ($existing !== null) {
            return false;
        }

        $this->em->persist($event);
        $this->em->flush();

        return true;
    }

    /** @param mixed $value */
    private function nonEmptyString($value, string $context): string
    {
        if (!\is_string($value) || \trim($value) === '') {
            throw new BillingException(sprintf('%s: обязательна непустая строка.', $context));
        }

        return $value;
    }

    /** @param mixed $value */
    private function nonNegativeInt($value, string $context): int
    {
        if (\is_bool($value) || (\is_string($value) && !preg_match('/^\d+$/', $value))) {
            throw new BillingException(sprintf('%s: ожидаются целые ≥ 0.', $context));
        }
        $int = (int) $value;
        if ($int < 0) {
            throw new BillingException(sprintf('%s: ожидаются целые ≥ 0.', $context));
        }

        return $int;
    }

    /** @param mixed $value */
    private function parseTimestamp($value, string $context): \DateTimeImmutable
    {
        if (!\is_string($value) || $value === '') {
            throw new BillingException(sprintf('%s: обязательна дата ISO-8601 (UTC).', $context));
        }

        try {
            return new \DateTimeImmutable($value, new \DateTimeZone("UTC"));
        } catch (\Exception $e) {
            throw new BillingException(sprintf('%s: некорректная дата ISO-8601 "%s".', $context, $value));
        }
    }
}
