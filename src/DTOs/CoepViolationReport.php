<?php

namespace audunru\ReportingApi\DTOs;

use audunru\ReportingApi\DTOs\Bodies\CoepViolationReportBody;

/**
 * Cross-Origin Embedder Policy violation report sent by the browser to a reporting endpoint.
 *
 * @see https://html.spec.whatwg.org/multipage/origin.html
 */
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
            url: self::string($data, 'url'),
            age: self::int($data, 'age'),
            userAgent: self::string($data, 'user_agent'),
            body: CoepViolationReportBody::fromArray(self::array($data, 'body') ?? []),
        );
    }
}
