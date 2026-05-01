<?php

namespace audunru\ReportingApi\DTOs\Bodies;

/**
 * Body of a crash report sent by the browser to a reporting endpoint.
 *
 * @see https://wicg.github.io/crash-reporting/
 */
class CrashReportBody
{
    public function __construct(
        public readonly ?string $reason,
        public readonly ?string $stack,
        public readonly ?bool $isTopLevel,
        public readonly ?string $visibilityState,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            reason: $data['reason'] ?? null,
            stack: $data['stack'] ?? null,
            isTopLevel: $data['is_top_level'] ?? null,
            visibilityState: $data['visibility_state'] ?? null,
        );
    }
}
