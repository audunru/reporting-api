<?php

namespace audunru\ReportingApi\Events;

use audunru\ReportingApi\Contracts\ReportEvent;
use audunru\ReportingApi\DTOs\CrashReport;

class CrashReportReceived implements ReportEvent
{
    public function __construct(private readonly array $report) {}

    public function getReport(): CrashReport
    {
        return CrashReport::fromArray($this->report);
    }

    public function getRawReport(): array
    {
        return $this->report;
    }
}
