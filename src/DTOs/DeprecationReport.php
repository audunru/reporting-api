<?php

namespace audunru\ReportingApi\DTOs;

use audunru\ReportingApi\DTOs\Bodies\DeprecationReportBody;

/**
 * Deprecation report sent by the browser to a reporting endpoint.
 *
 * @see https://wicg.github.io/deprecation-reporting/
 */
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
            url: self::string($data, 'url'),
            age: self::int($data, 'age'),
            userAgent: self::string($data, 'user_agent'),
            body: DeprecationReportBody::fromArray(self::array($data, 'body') ?? []),
        );
    }
}
