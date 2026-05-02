<?php

namespace audunru\ReportingApi\Listeners;

use audunru\ReportingApi\Contracts\ReportEvent;
use audunru\ReportingApi\DTOs\Report;
use Illuminate\Support\Facades\Log;

class LogReport
{
    protected string $channel = 'stack';

    public function handle(ReportEvent $event): void
    {
        $report = $event->getReport();

        if ($this->shouldExclude($report)) {
            return;
        }

        Log::channel($this->channel)->info('{type} report received at {url}', [
            'type' => $report->type,
            'url' => $report->url,
            'report' => $event->getRawReport(),
        ]);
    }

    protected function shouldExclude(Report $report): bool
    {
        return false;
    }
}
