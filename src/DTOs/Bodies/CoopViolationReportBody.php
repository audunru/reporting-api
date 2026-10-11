<?php

namespace audunru\ReportingApi\DTOs\Bodies;

use audunru\ReportingApi\DTOs\Concerns\ReadsTypedValues;

/**
 * Body of a Cross-Origin Opener Policy violation report sent by the browser to a reporting endpoint.
 * Models the union of navigation violation and access violation shapes.
 *
 * @see https://html.spec.whatwg.org/multipage/browsers.html
 */
class CoopViolationReportBody
{
    use ReadsTypedValues;

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
            type: self::string($data, 'type'),
            disposition: self::string($data, 'disposition'),
            effectivePolicy: self::string($data, 'effectivePolicy'),
            previousResponseURL: self::string($data, 'previousResponseURL'),
            nextResponseURL: self::string($data, 'nextResponseURL'),
            referrer: self::string($data, 'referrer'),
            property: self::string($data, 'property'),
            sourceFile: self::string($data, 'sourceFile'),
            lineNumber: self::int($data, 'lineNumber'),
            columnNumber: self::int($data, 'columnNumber'),
        );
    }
}
