<?php

namespace audunru\ReportingApi\DTOs;

use audunru\ReportingApi\DTOs\Bodies\DocumentPolicyViolationReportBody;

/**
 * Document Policy violation report sent by the browser to a reporting endpoint.
 *
 * @see https://wicg.github.io/document-policy/
 */
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
            url: self::string($data, 'url'),
            age: self::int($data, 'age'),
            userAgent: self::string($data, 'user_agent'),
            body: DocumentPolicyViolationReportBody::fromArray(self::array($data, 'body') ?? []),
        );
    }
}
