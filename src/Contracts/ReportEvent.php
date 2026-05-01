<?php

namespace audunru\ReportingApi\Contracts;

use audunru\ReportingApi\DTOs\Report;

interface ReportEvent
{
    public function getReport(): Report;

    public function getRawReport(): array;
}
