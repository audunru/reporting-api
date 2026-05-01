<?php

namespace audunru\ReportingApi\DTOs\Bodies;

/**
 * Body of a Cross-Origin Opener Policy violation report sent by the browser to a reporting endpoint.
 * Models the union of navigation violation and access violation shapes.
 *
 * @see https://html.spec.whatwg.org/multipage/browsers.html
 */
class CoopViolationReportBody
{
    public function __construct(
        public readonly ?string $type,
        public readonly ?string $disposition,
        public readonly ?string $effectivePolicy,
        public readonly ?string $previousResponseURL,
        public readonly ?string $nextResponseURL,
        public readonly ?string $referrer,
        public readonly ?string $property,
        public readonly ?string $sourceFile,
        public readonly ?int $lineNumber,
        public readonly ?int $columnNumber,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            type: $data['type'] ?? null,
            disposition: $data['disposition'] ?? null,
            effectivePolicy: $data['effectivePolicy'] ?? null,
            previousResponseURL: $data['previousResponseURL'] ?? null,
            nextResponseURL: $data['nextResponseURL'] ?? null,
            referrer: $data['referrer'] ?? null,
            property: $data['property'] ?? null,
            sourceFile: $data['sourceFile'] ?? null,
            lineNumber: $data['lineNumber'] ?? null,
            columnNumber: $data['columnNumber'] ?? null,
        );
    }
}
