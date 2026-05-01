<?php

namespace audunru\ReportingApi\Events;

use audunru\ReportingApi\Contracts\ReportEvent;
use audunru\ReportingApi\DTOs\CoepViolationReport;

class CoepReportReceived implements ReportEvent
{
    public function __construct(private readonly array $report) {}

    public function getReport(): CoepViolationReport
    {
        return CoepViolationReport::fromArray($this->report);
    }

    public function getRawReport(): array
    {
        return $this->report;
    }
}
