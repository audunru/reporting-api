<?php

namespace audunru\ReportingApi\Listeners;

use audunru\ReportingApi\DTOs\CspViolationReport;
use audunru\ReportingApi\Events\CspViolationReceived;
use audunru\ReportingApi\Listeners\Concerns\RedactsUrls;
use audunru\ReportingApi\Listeners\Concerns\SanitizesLogValues;
use Illuminate\Support\Facades\Log;

class LogCspViolation
{
    use RedactsUrls;
    use SanitizesLogValues;

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

        Log::channel($this->channel)->warning("CSP violation: {$this->sanitizeForLog($report->body->effectiveDirective)} blocked {$this->sanitizeForLog($this->redactUrl($report->body->blockedURL))} on {$this->sanitizeForLog($this->redactUrl($report->url))}", [
            'report' => $this->redactReport($event->getRawReport()),
        ]);
    }

    /**
     * Determine whether a CSP violation report should be excluded from logging.
     *
     * A genuine report always carries an effective directive and a blocked URL,
     * so anything missing either did not come from a browser.
     */
    protected function shouldExclude(CspViolationReport $report): bool
    {
        return empty($report->body->effectiveDirective) || empty($report->body->blockedURL);
    }
}
