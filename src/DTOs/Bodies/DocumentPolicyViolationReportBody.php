<?php

namespace audunru\ReportingApi\DTOs\Bodies;

use audunru\ReportingApi\DTOs\Concerns\ReadsTypedValues;

/**
 * Body of a Document Policy violation report sent by the browser to a reporting endpoint.
 *
 * @see https://wicg.github.io/document-policy/
 */
class DocumentPolicyViolationReportBody
{
    use ReadsTypedValues;

    public function __construct(
        public readonly ?string $featureId,
        public readonly ?string $disposition,
        public readonly ?string $sourceFile,
        public readonly ?int $lineNumber,
        public readonly ?int $columnNumber,
        public readonly ?string $message,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            featureId: self::string($data, 'featureId') ?? self::string($data, 'policyId'), // spec: featureId; older browsers may send policyId
            disposition: self::string($data, 'disposition'),
            sourceFile: self::string($data, 'sourceFile'),
            lineNumber: self::int($data, 'lineNumber'),
            columnNumber: self::int($data, 'columnNumber'),
            message: self::string($data, 'message'),
        );
    }
}
