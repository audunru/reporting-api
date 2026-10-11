<?php

namespace audunru\ReportingApi\Listeners\Concerns;

/**
 * Make untrusted report values safe to put in a log message.
 */
trait SanitizesLogValues
{
    /**
     * Replace control characters and line separators with spaces, so a value cannot start a new log line.
     */
    protected function sanitizeForLog(?string $value): string
    {
        return preg_replace('/[\p{Cc}\p{Zl}\p{Zp}]/u', ' ', $value ?? '');
    }
}
