<?php

namespace audunru\ReportingApi\DTOs\Bodies;

/**
 * Body of a deprecation report sent by the browser to a reporting endpoint.
 *
 * @see https://wicg.github.io/deprecation-reporting/
 */
class DeprecationReportBody
{
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $message,
        public readonly ?string $sourceFile,
        public readonly ?int $lineNumber,
        public readonly ?int $columnNumber,
        public readonly ?string $anticipatedRemoval,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'] ?? null,
            message: $data['message'] ?? null,
            sourceFile: $data['sourceFile'] ?? null,
            lineNumber: $data['lineNumber'] ?? null,
            columnNumber: $data['columnNumber'] ?? null,
            anticipatedRemoval: $data['anticipatedRemoval'] ?? null,
        );
    }
}
