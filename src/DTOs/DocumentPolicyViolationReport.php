<?php

namespace audunru\ReportingApi\DTOs;

use audunru\ReportingApi\DTOs\Bodies\DocumentPolicyViolationReportBody;

class DocumentPolicyViolationReport extends Report
{
    public function __construct(
        ?string $url,
        ?int $age,
        ?string $userAgent,
        public readonly DocumentPolicyViolationReportBody $body,
    ) {
        parent::__construct('document-policy-violation', $url, $age, $userAgent);
    }

    public static function fromArray(array $data): static
    {
        return new static(
            url: $data['url'] ?? null,
            age: $data['age'] ?? null,
            userAgent: $data['user_agent'] ?? null,
            body: DocumentPolicyViolationReportBody::fromArray($data['body'] ?? []),
        );
    }
}
