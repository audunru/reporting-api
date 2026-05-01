<?php

namespace audunru\ReportingApi\DTOs\Bodies;

/**
 * Body of an intervention report sent by the browser to a reporting endpoint.
 *
 * @see https://wicg.github.io/intervention-reporting/
 */
class InterventionReportBody
{
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $message,
        public readonly ?string $sourceFile,
        public readonly ?int $lineNumber,
        public readonly ?int $columnNumber,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'] ?? null,
            message: $data['message'] ?? null,
            sourceFile: $data['sourceFile'] ?? null,
            lineNumber: $data['lineNumber'] ?? null,
            columnNumber: $data['columnNumber'] ?? null,
        );
    }
}
