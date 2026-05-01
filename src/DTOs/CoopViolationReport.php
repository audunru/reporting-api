<?php

namespace audunru\ReportingApi\DTOs;

use audunru\ReportingApi\DTOs\Bodies\CoopViolationReportBody;

class CoopViolationReport extends Report
{
    public function __construct(
        ?string $url,
        ?int $age,
        ?string $userAgent,
        public readonly CoopViolationReportBody $body,
    ) {
        parent::__construct('coop', $url, $age, $userAgent);
    }

    public static function fromArray(array $data): static
    {
        return new static(
            url: $data['url'] ?? null,
            age: $data['age'] ?? null,
            userAgent: $data['user_agent'] ?? null,
            body: CoopViolationReportBody::fromArray($data['body'] ?? []),
        );
    }
}
