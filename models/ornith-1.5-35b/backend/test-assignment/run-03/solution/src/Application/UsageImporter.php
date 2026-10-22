<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Entity\UsageEvent;
use App\Domain\Repository\UsageEventRepository;

/**
 * Загрузка событий использования из JSONL-файла.
 *
 * События dedup'ятся по ключу (provider, provider_request_id): повторная
 * доставки уже известного события целиком игнорируется (никаких обновлений,
 * никаких доначислений). Некорректная строка — исключение (код 1).
 */
final class UsageImporter
{
    public function __construct(
        private UsageEventRepository $events,
    ) {
    }

    /**
     * @return array{loaded:int, duplicate:int}
     */
    public function importFromFile(string $filePath): array
    {
        return $this->import($this->readFile($filePath));
    }

    /**
     * @return array{loaded:int, duplicate:int}
     */
    public function import(string $content): array
    {
        $loaded = 0;
        $duplicate = 0;

        foreach ($this->lines($content) as $line) {
            $rec = $this->parseLine($line);

            $existing = $this->events->findByKey($rec['provider'], $rec['provider_request_id']);
            if (null !== $existing) {
                ++$duplicate;
                continue;
            }

            $event = new UsageEvent(new \DateTimeImmutable($rec['ts']));
            $event->providerId = $rec['provider'];
            $event->providerRequestId = $rec['provider_request_id'];
            $event->user = $rec['user'];
            $event->model = $rec['provider'] . '/' . $rec['model'];
            $event->tokensInput = $rec['tokens_input'];
            $event->tokensOutput = $rec['tokens_output'];
            $event->tokensCacheRead = $rec['tokens_cache_read'];
            $event->tokensCacheWrite = $rec['tokens_cache_write'];
            $event->rated = false;
            $event->costUsd = null;
            $event->priceRowId = null;
            $this->events->save($event);

            ++$loaded;
        }

        return ['loaded' => $loaded, 'duplicate' => $duplicate];
    }

    /**
     * @return iterable<int,string>
     */
    private function lines(string $content): iterable
    {
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        foreach (explode("\n", $content) as $line) {
            if ('' !== trim($line)) {
                yield $line;
            }
        }
    }

    /**
     * @return array{provider:string,provider_request_id:string,model:string,user:string,ts:string,tokens_input:int,tokens_output:int,tokens_cache_read:int,tokens_cache_write:int}
     */
    private function parseLine(string $line): array
    {
        $rec = json_decode($line, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($rec)) {
            throw new \RuntimeException('Событие использования: строка не является объектом JSON.');
        }

        foreach (['provider', 'provider_request_id', 'model', 'user', 'ts'] as $field) {
            if (!isset($rec[$field]) || !is_string($rec[$field]) || '' === trim($rec[$field])) {
                throw new \RuntimeException(
                    'Событие использования: отсутствует обязательное поле "' . $field . '".',
                );
            }
        }

        foreach (['tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write'] as $key) {
            if (!isset($rec[$key]) || !is_int($rec[$key]) || $rec[$key] < 0) {
                throw new \RuntimeException(
                    'Событие использования: поле "' . $key
                        . '" должно быть целым неотрицательным числом.',
                );
            }
        }

        // Момент ts — ISO-8601 UTC; бросит \RuntimeException при неверном формате.
        new \DateTimeImmutable($rec['ts']);

        return [
            'provider' => $rec['provider'],
            'provider_request_id' => $rec['provider_request_id'],
            'model' => $rec['model'],
            'user' => $rec['user'],
            'ts' => $rec['ts'],
            'tokens_input' => $rec['tokens_input'],
            'tokens_output' => $rec['tokens_output'],
            'tokens_cache_read' => $rec['tokens_cache_read'],
            'tokens_cache_write' => $rec['tokens_cache_write'],
        ];
    }

    private function readFile(string $filePath): string
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new \RuntimeException('Файл событий не найден или недоступен: ' . $filePath);
        }
        $content = file_get_contents($filePath);
        if (false === $content) {
            throw new \RuntimeException('Не удалось прочитать файл событий: ' . $filePath);
        }

        return $content;
    }
}
