<?php

namespace audunru\ReportingApi\Listeners;

use audunru\ReportingApi\Contracts\ReportEvent;
use audunru\ReportingApi\DTOs\Report;
use audunru\ReportingApi\Listeners\Concerns\SanitizesLogValues;
use Illuminate\Support\Facades\Log;

class LogReport
{
    use SanitizesLogValues;

    protected string $channel = 'stack';

    public function handle(ReportEvent $event): void
    {
        $report = $event->getReport();

        if ($this->shouldExclude($report)) {
            return;
        }

        Log::channel($this->channel)->warning("{$this->sanitizeForLog($report->type)} report received at {$this->sanitizeUrlForLog($report->url)}", [
            'report' => $event->getRawReport(),
        ]);
    }

    protected function shouldExclude(Report $report): bool
    {
        return $report->type === 'csp-violation';
    }
}
