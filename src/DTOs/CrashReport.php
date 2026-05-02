<?php

namespace audunru\ReportingApi\DTOs;

use audunru\ReportingApi\DTOs\Bodies\CrashReportBody;

/**
 * Crash report sent by the browser to a reporting endpoint.
 *
 * @see https://wicg.github.io/crash-reporting/
 */
class CrashReport extends Report
{
    public function __construct(
        ?string $url,
        ?int $age,
        ?string $userAgent,
        public readonly CrashReportBody $body,
    ) {
        parent::__construct('crash', $url, $age, $userAgent);
    }

    public static function fromArray(array $data): static
    {
        return new static(
            url: $data['url'] ?? null,
            age: $data['age'] ?? null,
            userAgent: $data['user_agent'] ?? null,
            body: CrashReportBody::fromArray($data['body'] ?? []),
        );
    }
}
