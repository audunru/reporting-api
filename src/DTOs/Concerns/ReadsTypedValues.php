<?php

namespace audunru\ReportingApi\DTOs\Concerns;

/**
 * Read values from untrusted report data. A value with the wrong JSON type becomes null.
 */
trait ReadsTypedValues
{
    protected static function string(array $data, string $key): ?string
    {
        return is_string($data[$key] ?? null) ? $data[$key] : null;
    }

    protected static function int(array $data, string $key): ?int
    {
        return is_int($data[$key] ?? null) ? $data[$key] : null;
    }

    protected static function float(array $data, string $key): ?float
    {
        $value = $data[$key] ?? null;

        return is_int($value) || is_float($value) ? (float) $value : null;
    }

    protected static function bool(array $data, string $key): ?bool
    {
        return is_bool($data[$key] ?? null) ? $data[$key] : null;
    }

    protected static function array(array $data, string $key): ?array
    {
        return is_array($data[$key] ?? null) ? $data[$key] : null;
    }
}
