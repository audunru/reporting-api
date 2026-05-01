<?php

namespace audunru\ReportingApi\DTOs\Bodies;

/**
 * Body of a Document Policy violation report sent by the browser to a reporting endpoint.
 *
 * @see https://wicg.github.io/document-policy/
 */
class DocumentPolicyViolationReportBody
{
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
            featureId: $data['featureId'] ?? $data['policyId'] ?? null, // spec: featureId; older browsers may send policyId
            disposition: $data['disposition'] ?? null,
            sourceFile: $data['sourceFile'] ?? null,
            lineNumber: $data['lineNumber'] ?? null,
            columnNumber: $data['columnNumber'] ?? null,
            message: $data['message'] ?? null,
        );
    }
}
