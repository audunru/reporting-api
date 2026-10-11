<?php

namespace audunru\ReportingApi\DTOs\Bodies;

use audunru\ReportingApi\DTOs\Concerns\ReadsTypedValues;

/**
 * Body of a Cross-Origin Embedder Policy violation report sent by the browser to a reporting endpoint.
 *
 * @see https://html.spec.whatwg.org/multipage/origin.html
 */
class CoepViolationReportBody
{
    use ReadsTypedValues;

    public function __construct(
        public readonly ?string $type,
        public readonly ?string $blockedURL,
        public readonly ?string $destination,
        public readonly ?string $disposition,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            type: self::string($data, 'type'),
            blockedURL: self::string($data, 'blockedURL'),
            destination: self::string($data, 'destination'),
            disposition: self::string($data, 'disposition'),
        );
    }
}
