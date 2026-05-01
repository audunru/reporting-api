<?php

namespace audunru\ReportingApi\DTOs;

abstract class Report
{
    public function __construct(
        public readonly string $type,
        public readonly ?string $url,
        public readonly ?int $age,
        public readonly ?string $userAgent,
    ) {}
}
