<?php

namespace audunru\ReportingApi\DTOs;

use audunru\ReportingApi\DTOs\Bodies\CspViolationReportBody;

/**
 * Content Security Policy violation report sent by the browser to a reporting endpoint.
 *
 * @see https://www.w3.org/TR/CSP3/
 */
class CspViolationReport extends Report
{
    public function __construct(
        ?string $url,
        ?int $age,
        ?string $userAgent,
        public readonly CspViolationReportBody $body,
    ) {
        parent::__construct('csp-violation', $url, $age, $userAgent);
    }

    public static function fromArray(array $data): static
    {
        return new static(
            url: $data['url'] ?? null,
            age: $data['age'] ?? null,
            userAgent: $data['user_agent'] ?? null,
            body: CspViolationReportBody::fromArray($data['body'] ?? []),
        );
    }
}
