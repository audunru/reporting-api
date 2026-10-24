<?php

namespace audunru\ReportingApi\Listeners\Concerns;

/**
 * Remove the parts of a URL that can hold tokens or personal data before it is logged.
 */
trait RedactsUrls
{
    /**
     * Remove the query string and the fragment. Override to also redact parts of the path.
     */
    protected function redactUrl(?string $url): string
    {
        return preg_replace('/[?#].*$/s', '', $url ?? '');
    }

    /**
     * Apply redactUrl() to every value in the raw report that is an absolute URL.
     */
    protected function redactReport(array $report): array
    {
        array_walk_recursive($report, function (mixed &$value): void {
            if (is_string($value) && preg_match('#^[a-z][a-z0-9+.-]*://#i', $value)) {
                $value = $this->redactUrl($value);
            }
        });

        return $report;
    }
}
