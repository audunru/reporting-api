<?php

namespace audunru\ReportingApi\DTOs\Bodies;

use audunru\ReportingApi\DTOs\Concerns\ReadsTypedValues;

/**
 * Body of a CSP violation report sent by the browser to a reporting endpoint.
 *
 * @see https://www.w3.org/TR/CSP3/
 */
class CspViolationReportBody
{
    use ReadsTypedValues;

    public function __construct(
        public readonly ?string $blockedURL,
        public readonly ?int $columnNumber,
        public readonly ?string $disposition,
        public readonly ?string $documentURL,
        public readonly ?string $effectiveDirective,
        public readonly ?int $lineNumber,
        public readonly ?string $originalPolicy,
        public readonly ?string $referrer,
        public readonly ?string $sample,
        public readonly ?string $sourceFile,
        public readonly ?int $statusCode,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            blockedURL: self::string($data, 'blockedURL'),
            columnNumber: self::int($data, 'columnNumber'),
            disposition: self::string($data, 'disposition'),
            documentURL: self::string($data, 'documentURL'),
            effectiveDirective: self::string($data, 'effectiveDirective'),
            lineNumber: self::int($data, 'lineNumber'),
            originalPolicy: self::string($data, 'originalPolicy'),
            referrer: self::string($data, 'referrer'),
            sample: self::string($data, 'sample'),
            sourceFile: self::string($data, 'sourceFile'),
            statusCode: self::int($data, 'statusCode'),
        );
    }
}
