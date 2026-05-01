<?php

namespace audunru\ReportingApi\Events;

use audunru\ReportingApi\Contracts\ReportEvent;
use audunru\ReportingApi\DTOs\DocumentPolicyViolationReport;

class DocumentPolicyViolationReceived implements ReportEvent
{
    public function __construct(private readonly array $report) {}

    public function getReport(): DocumentPolicyViolationReport
    {
        return DocumentPolicyViolationReport::fromArray($this->report);
    }

    public function getRawReport(): array
    {
        return $this->report;
    }
}
