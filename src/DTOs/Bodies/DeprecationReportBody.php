<?php

namespace audunru\ReportingApi\DTOs\Bodies;

use audunru\ReportingApi\DTOs\Concerns\ReadsTypedValues;

/**
 * Body of a deprecation report sent by the browser to a reporting endpoint.
 *
 * @see https://wicg.github.io/deprecation-reporting/
 */
class DeprecationReportBody
{
    use ReadsTypedValues;

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
            id: self::string($data, 'id'),
            message: self::string($data, 'message'),
            sourceFile: self::string($data, 'sourceFile'),
            lineNumber: self::int($data, 'lineNumber'),
            columnNumber: self::int($data, 'columnNumber'),
            anticipatedRemoval: self::string($data, 'anticipatedRemoval'),
        );
    }
}
