<?php

declare(strict_types=1);

namespace App\Infrastructure\Import;

use App\Application\Import\UsageEventDefinition;
use App\Domain\Usage\TokenUsage;
use Generator;
use InvalidArgumentException;
use JsonException;

use function array_key_exists;
use function array_is_list;
use function fclose;
use function feof;
use function fgets;
use function fopen;
use function is_array;
use function is_int;
use function is_string;
use function json_decode;
use function rtrim;
use function sprintf;
use function str_starts_with;
use function substr;
use function trim;

final class UsageFileParser
{
    private const int JSON_DEPTH = 512;

    /**
     * @return Generator<int, UsageEventDefinition>
     */
    public function parse(string $path): Generator
    {
        $handle = @fopen($path, 'rb');
        if ($handle === false) {
            throw new InvalidArgumentException(sprintf('Cannot read usage file "%s".', $path));
        }

        try {
            $lineNumber = 0;

            while (($line = fgets($handle)) !== false) {
                ++$lineNumber;
                $line = rtrim($line, "\r\n");
                if ($lineNumber === 1 && str_starts_with($line, "\xEF\xBB\xBF")) {
                    $line = substr($line, 3);
                }

                if (trim($line) === '') {
                    continue;
                }

                try {
                    $document = json_decode($line, true, self::JSON_DEPTH, JSON_THROW_ON_ERROR);
                } catch (JsonException $exception) {
                    throw new InvalidArgumentException(sprintf(
                        'Invalid JSON on line %d of "%s": %s',
                        $lineNumber,
                        $path,
                        $exception->getMessage(),
                    ), 0, $exception);
                }

                if (!is_array($document) || array_is_list($document)) {
                    throw new InvalidArgumentException(sprintf(
                        'Line %d of "%s" must contain a JSON object.',
                        $lineNumber,
                        $path,
                    ));
                }

                try {
                    yield $this->parseEvent($document);
                } catch (InvalidArgumentException $exception) {
                    throw new InvalidArgumentException(sprintf(
                        'Invalid usage event on line %d of "%s": %s',
                        $lineNumber,
                        $path,
                        $exception->getMessage(),
                    ), 0, $exception);
                }
            }

            if (!feof($handle)) {
                throw new InvalidArgumentException(sprintf('Failed while reading usage file "%s".', $path));
            }
        } finally {
            fclose($handle);
        }
    }

    /**
     * @param array<string, mixed> $document
     */
    private function parseEvent(array $document): UsageEventDefinition
    {
        $provider = $this->requiredString($document, 'provider');
        $providerRequestId = $this->requiredString($document, 'provider_request_id');
        $model = $this->requiredString($document, 'model');
        $user = $this->requiredString($document, 'user', false);
        $occurredAt = Iso8601::parse($this->requiredString($document, 'ts'), 'ts');

        $usage = new TokenUsage(
            $this->tokens($document, 'tokens_input'),
            $this->tokens($document, 'tokens_output'),
            $this->tokens($document, 'tokens_cache_read'),
            $this->tokens($document, 'tokens_cache_write'),
        );

        return new UsageEventDefinition($provider, $providerRequestId, $model, $user, $occurredAt, $usage);
    }

    /**
     * @param array<string, mixed> $document
     */
    private function tokens(array $document, string $key): int
    {
        if (!array_key_exists($key, $document) || !is_int($document[$key]) || $document[$key] < 0) {
            throw new InvalidArgumentException(sprintf('Field "%s" must be a non-negative integer.', $key));
        }

        return $document[$key];
    }

    /**
     * @param array<string, mixed> $document
     */
    private function requiredString(array $document, string $key, bool $nonEmpty = true): string
    {
        if (!array_key_exists($key, $document) || !is_string($document[$key])) {
            throw new InvalidArgumentException(sprintf('Field "%s" is required as a string.', $key));
        }

        if ($nonEmpty && $document[$key] === '') {
            throw new InvalidArgumentException(sprintf('Field "%s" must not be empty.', $key));
        }

        return $document[$key];
    }
}
