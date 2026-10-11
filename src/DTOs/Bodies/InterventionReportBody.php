<?php

namespace audunru\ReportingApi\DTOs\Bodies;

use audunru\ReportingApi\DTOs\Concerns\ReadsTypedValues;

/**
 * Body of an intervention report sent by the browser to a reporting endpoint.
 *
 * @see https://wicg.github.io/intervention-reporting/
 */
class InterventionReportBody
{
    use ReadsTypedValues;

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
            id: self::string($data, 'id'),
            message: self::string($data, 'message'),
            sourceFile: self::string($data, 'sourceFile'),
            lineNumber: self::int($data, 'lineNumber'),
            columnNumber: self::int($data, 'columnNumber'),
        );
    }
}
