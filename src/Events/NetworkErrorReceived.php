<?php

namespace audunru\ReportingApi\Events;

use audunru\ReportingApi\Contracts\ReportEvent;
use audunru\ReportingApi\DTOs\NetworkErrorReport;

class NetworkErrorReceived implements ReportEvent
{
    public function __construct(private readonly array $report) {}

    public function getReport(): NetworkErrorReport
    {
        return NetworkErrorReport::fromArray($this->report);
    }

    public function getRawReport(): array
    {
        return $this->report;
    }
}
