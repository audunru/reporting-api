<?php

namespace audunru\ReportingApi\DTOs;

use audunru\ReportingApi\DTOs\Bodies\InterventionReportBody;

/**
 * Intervention report sent by the browser to a reporting endpoint.
 *
 * @see https://wicg.github.io/intervention-reporting/
 */
class InterventionReport extends Report
{
    public function __construct(
        ?string $url,
        ?int $age,
        ?string $userAgent,
        public readonly InterventionReportBody $body,
    ) {
        parent::__construct('intervention', $url, $age, $userAgent);
    }

    public static function fromArray(array $data): static
    {
        return new static(
            url: $data['url'] ?? null,
            age: $data['age'] ?? null,
            userAgent: $data['user_agent'] ?? null,
            body: InterventionReportBody::fromArray($data['body'] ?? []),
        );
    }
}
