<?php

namespace audunru\ReportingApi\DTOs;

use audunru\ReportingApi\DTOs\Bodies\NetworkErrorReportBody;

class NetworkErrorReport extends Report
{
    public function __construct(
        ?string $url,
        ?int $age,
        ?string $userAgent,
        public readonly NetworkErrorReportBody $body,
    ) {
        parent::__construct('network-error', $url, $age, $userAgent);
    }

    public static function fromArray(array $data): static
    {
        return new static(
            url: $data['url'] ?? null,
            age: $data['age'] ?? null,
            userAgent: $data['user_agent'] ?? null,
            body: NetworkErrorReportBody::fromArray($data['body'] ?? []),
        );
    }
}
