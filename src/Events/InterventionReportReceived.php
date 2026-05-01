<?php

namespace audunru\ReportingApi\Events;

use audunru\ReportingApi\Contracts\ReportEvent;
use audunru\ReportingApi\DTOs\InterventionReport;

class InterventionReportReceived implements ReportEvent
{
    public function __construct(private readonly array $report) {}

    public function getReport(): InterventionReport
    {
        return InterventionReport::fromArray($this->report);
    }

    public function getRawReport(): array
    {
        return $this->report;
    }
}
