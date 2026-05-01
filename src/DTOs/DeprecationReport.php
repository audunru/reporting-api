<?php

namespace audunru\ReportingApi\DTOs;

use audunru\ReportingApi\DTOs\Bodies\DeprecationReportBody;

class DeprecationReport extends Report
{
    public function __construct(
        ?string $url,
        ?int $age,
        ?string $userAgent,
        public readonly DeprecationReportBody $body,
    ) {
        parent::__construct('deprecation', $url, $age, $userAgent);
    }

    public static function fromArray(array $data): static
    {
        return new static(
            url: $data['url'] ?? null,
            age: $data['age'] ?? null,
            userAgent: $data['user_agent'] ?? null,
            body: DeprecationReportBody::fromArray($data['body'] ?? []),
        );
    }
}
