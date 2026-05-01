<?php

namespace audunru\ReportingApi\DTOs;

use audunru\ReportingApi\DTOs\Bodies\CoepViolationReportBody;

class CoepViolationReport extends Report
{
    public function __construct(
        ?string $url,
        ?int $age,
        ?string $userAgent,
        public readonly CoepViolationReportBody $body,
    ) {
        parent::__construct('coep', $url, $age, $userAgent);
    }

    public static function fromArray(array $data): static
    {
        return new static(
            url: $data['url'] ?? null,
            age: $data['age'] ?? null,
            userAgent: $data['user_agent'] ?? null,
            body: CoepViolationReportBody::fromArray($data['body'] ?? []),
        );
    }
}
