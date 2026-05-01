<?php

namespace audunru\ReportingApi\DTOs\Bodies;

/**
 * Body of a Cross-Origin Embedder Policy violation report sent by the browser to a reporting endpoint.
 *
 * @see https://html.spec.whatwg.org/multipage/origin.html
 */
class CoepViolationReportBody
{
    public function __construct(
        public readonly ?string $type,
        public readonly ?string $blockedURL,
        public readonly ?string $destination,
        public readonly ?string $disposition,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            type: $data['type'] ?? null,
            blockedURL: $data['blockedURL'] ?? null,
            destination: $data['destination'] ?? null,
            disposition: $data['disposition'] ?? null,
        );
    }
}
