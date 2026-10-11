<?php

namespace audunru\ReportingApi\DTOs;

use audunru\ReportingApi\DTOs\Concerns\ReadsTypedValues;

abstract class Report
{
    use ReadsTypedValues;

    public function __construct(
        public readonly string $type,
        public readonly ?string $url,
        public readonly ?int $age,
        public readonly ?string $userAgent,
    ) {}
}
