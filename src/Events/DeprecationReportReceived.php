<?php

namespace audunru\ReportingApi\Events;

use audunru\ReportingApi\Contracts\ReportEvent;
use audunru\ReportingApi\DTOs\DeprecationReport;

class DeprecationReportReceived implements ReportEvent
{
    public function __construct(private readonly array $report) {}

    public function getReport(): DeprecationReport
    {
        return DeprecationReport::fromArray($this->report);
    }

    public function getRawReport(): array
    {
        return $this->report;
    }
}
