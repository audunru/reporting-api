<?php

namespace audunru\ReportingApi\Events;

use audunru\ReportingApi\Contracts\ReportEvent;
use audunru\ReportingApi\DTOs\CspViolationReport;

class CspViolationReceived implements ReportEvent
{
    public function __construct(private readonly array $report) {}

    public function getReport(): CspViolationReport
    {
        return CspViolationReport::fromArray($this->report);
    }

    public function getRawReport(): array
    {
        return $this->report;
    }
}
