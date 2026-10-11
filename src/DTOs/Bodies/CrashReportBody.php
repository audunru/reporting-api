<?php

namespace audunru\ReportingApi\DTOs\Bodies;

use audunru\ReportingApi\DTOs\Concerns\ReadsTypedValues;

/**
 * Body of a crash report sent by the browser to a reporting endpoint.
 *
 * @see https://wicg.github.io/crash-reporting/
 */
class CrashReportBody
{
    use ReadsTypedValues;

    public function __construct(
        public readonly ?string $reason,
        public readonly ?string $stack,
        public readonly ?bool $isTopLevel,
        public readonly ?string $visibilityState,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            reason: self::string($data, 'reason'),
            stack: self::string($data, 'stack'),
            isTopLevel: self::bool($data, 'is_top_level'),
            visibilityState: self::string($data, 'visibility_state'),
        );
    }
}
