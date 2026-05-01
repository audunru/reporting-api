<?php

namespace audunru\ReportingApi\Events;

use audunru\ReportingApi\Contracts\ReportEvent;
use audunru\ReportingApi\DTOs\GenericReport;

class GenericReportReceived implements ReportEvent
{
    public function __construct(private readonly array $report) {}

    public function getReport(): GenericReport
    {
        return GenericReport::fromArray($this->report);
    }

    public function getRawReport(): array
    {
        return $this->report;
    }
}
