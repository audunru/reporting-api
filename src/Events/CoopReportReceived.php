<?php

namespace audunru\ReportingApi\Events;

use audunru\ReportingApi\Contracts\ReportEvent;
use audunru\ReportingApi\DTOs\CoopViolationReport;

class CoopReportReceived implements ReportEvent
{
    public function __construct(private readonly array $report) {}

    public function getReport(): CoopViolationReport
    {
        return CoopViolationReport::fromArray($this->report);
    }

    public function getRawReport(): array
    {
        return $this->report;
    }
}
