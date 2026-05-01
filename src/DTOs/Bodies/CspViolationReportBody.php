<?php

namespace audunru\ReportingApi\DTOs\Bodies;

/**
 * Body of a CSP violation report sent by the browser to a reporting endpoint.
 *
 * @see https://www.w3.org/TR/CSP3/
 */
class CspViolationReportBody
{
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
            blockedURL: $data['blockedURL'] ?? null,
            columnNumber: $data['columnNumber'] ?? null,
            disposition: $data['disposition'] ?? null,
            documentURL: $data['documentURL'] ?? null,
            effectiveDirective: $data['effectiveDirective'] ?? null,
            lineNumber: $data['lineNumber'] ?? null,
            originalPolicy: $data['originalPolicy'] ?? null,
            referrer: $data['referrer'] ?? null,
            sample: $data['sample'] ?? null,
            sourceFile: $data['sourceFile'] ?? null,
            statusCode: $data['statusCode'] ?? null,
        );
    }
}
