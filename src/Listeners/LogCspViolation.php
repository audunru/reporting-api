<?php

namespace audunru\ReportingApi\Listeners;

use audunru\ReportingApi\DTOs\CspViolationReport;
use audunru\ReportingApi\Events\CspViolationReceived;
use Illuminate\Support\Facades\Log;

class LogCspViolation
{
    protected string $channel = 'stack';

    /**
     * Log an incoming CSP violation report unless it should be excluded.
     */
    public function handle(CspViolationReceived $event): void
    {
        $report = $event->getReport();

        if ($this->shouldExclude($report)) {
            return;
        }

        Log::channel($this->channel)->warning('CSP violation: {directive} blocked {url}', [
            'directive' => $report->body->effectiveDirective,
            'url' => $report->body->blockedURL,
            'page' => $report->url,
        ]);
    }

    /**
     * Determine whether a CSP violation report should be excluded from logging.
     */
    protected function shouldExclude(CspViolationReport $report): bool
    {
        return false;
    }
}
