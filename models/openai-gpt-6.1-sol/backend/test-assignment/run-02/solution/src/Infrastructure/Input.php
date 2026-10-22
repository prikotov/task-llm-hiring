<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Domain\OffPeak;
use App\Domain\Price;
use App\Domain\TokenUsage;
use App\Domain\UsageEvent;

final class Input
{
    public static function instant(string $value): \DateTimeImmutable
    {
        if (!preg_match('/\A[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}(?:\.[0-9]{1,6})?Z\z/', $value)) {
            throw new \InvalidArgumentException('Ожидается ISO-8601 UTC с Z (точность до микросекунд): ' . $value);
        }
        $format = str_contains($value, '.') ? '!Y-m-d\TH:i:s.u\Z' : '!Y-m-d\TH:i:s\Z';
        $date = \DateTimeImmutable::createFromFormat($format, $value, new \DateTimeZone('UTC'));
        $errors = \DateTimeImmutable::getLastErrors();
        if ($date === false || ($errors !== false && ($errors['warning_count'] || $errors['error_count']))) {
            throw new \InvalidArgumentException('Некорректная дата: ' . $value);
        }

        return $date;
    }

    public static function displayInstant(\DateTimeImmutable $instant): string
    {
        $instant = $instant->setTimezone(new \DateTimeZone('UTC'));

        return $instant->format('u') === '000000'
            ? $instant->format('Y-m-d\TH:i:s\Z')
            : rtrim($instant->format('Y-m-d\TH:i:s.u'), '0') . 'Z';
    }

    public function prices(string $file, bool $correction = false): array
    {
        $contents = @file_get_contents($file);
        if ($contents === false) {
            throw new \InvalidArgumentException('Не удалось прочитать файл: ' . $file);
        }
        $root = $this->decode($contents);
        $providers = $models = $prices = [];
        foreach ($this->list($root, 'providers', $correction) as $value) {
            $row = $this->object($value);
            $code = $this->text($row, 'code');
            $name = $this->text($row, 'name');
            $offPeak = isset($row['off_peak']) ? $this->object($row['off_peak']) : null;
            if ($offPeak !== null) {
                $offPeak = [
                    'window_start' => $this->text($offPeak, 'window_start'),
                    'window_end' => $this->text($offPeak, 'window_end'),
                    'multiplier' => $this->text($offPeak, 'multiplier', false),
                ];
                new OffPeak($offPeak['window_start'], $offPeak['window_end'], $offPeak['multiplier']);
            }
            $providers[$code] = ['code' => $code, 'name' => $name, 'off_peak' => $offPeak];
        }
        foreach ($this->list($root, 'models', $correction) as $value) {
            $row = $this->object($value);
            $provider = $this->text($row, 'provider');
            $code = $this->text($row, 'code');
            $models[json_encode([$provider, $code], JSON_THROW_ON_ERROR)] = ['provider' => $provider, 'code' => $code];
        }
        foreach ($this->list($root, 'prices') as $value) {
            $row = $this->object($value);
            $provider = $this->text($row, 'provider');
            $model = $this->text($row, 'model');
            $date = self::instant($this->text($row, 'valid_from'));
            $currency = $this->text($row, 'currency');
            $unit = $this->text($row, 'unit');
            $rates = $this->object($row['rates'] ?? null);
            Price::validate($currency, $unit, $rates);
            $key = json_encode([$provider, $model, $date->format('Y-m-d H:i:s.u')], JSON_THROW_ON_ERROR);
            $prices[$key] = compact('provider', 'model', 'date', 'currency', 'unit', 'rates');
        }

        return compact('providers', 'models', 'prices');
    }

    /** @return \Generator<UsageEvent> */
    public function usage(string $file): \Generator
    {
        $handle = @fopen($file, 'rb');
        if ($handle === false) {
            throw new \InvalidArgumentException('Не удалось прочитать файл: ' . $file);
        }
        $number = 0;
        try {
            while (($line = fgets($handle)) !== false) {
                ++$number;
                try {
                    $row = $this->decode($line);
                    $counts = [];
                    foreach (TokenUsage::KINDS as $kind) {
                        $value = $row['tokens_' . $kind] ?? null;
                        if (!is_int($value) || $value < 0) {
                            throw new \InvalidArgumentException('tokens_' . $kind . ' должен быть целым числом >= 0 в диапазоне PHP int.');
                        }
                        $counts[] = $value;
                    }
                    yield new UsageEvent(
                        $this->text($row, 'provider'),
                        $this->text($row, 'provider_request_id'),
                        $this->text($row, 'model'),
                        $this->text($row, 'user'),
                        self::instant($this->text($row, 'ts')),
                        new TokenUsage(...$counts),
                    );
                } catch (\InvalidArgumentException | \JsonException $exception) {
                    throw new \InvalidArgumentException('Строка ' . $number . ': ' . $exception->getMessage(), 0, $exception);
                }
            }
            if (!feof($handle)) {
                throw new \RuntimeException('Ошибка чтения файла: ' . $file);
            }
        } finally {
            fclose($handle);
        }
    }

    private function decode(string $json): array
    {
        return $this->object(json_decode($json, false, 512, JSON_THROW_ON_ERROR));
    }

    private function object(mixed $value): array
    {
        if (!$value instanceof \stdClass) {
            throw new \InvalidArgumentException('Ожидался JSON-объект.');
        }

        return (array) $value;
    }

    private function list(array $row, string $key, bool $optional = false): array
    {
        if ($optional && !array_key_exists($key, $row)) {
            return [];
        }
        $value = $row[$key] ?? null;
        if (!is_array($value) || !array_is_list($value)) {
            throw new \InvalidArgumentException('Ожидался массив ' . $key . '.');
        }

        return $value;
    }

    private function text(array $row, string $key, bool $bounded = true): string
    {
        $value = $row[$key] ?? null;
        if (!is_string($value) || $value === '' || str_contains($value, "\0") || ($bounded && !preg_match('/\A.{1,255}\z/us', $value))) {
            throw new \InvalidArgumentException('Поле ' . $key . ' должно быть непустой строкой' . ($bounded ? ' до 255 символов.' : '.'));
        }

        return $value;
    }
}
