<?php

namespace audunru\ReportingApi\DTOs;

class GenericReport extends Report
{
    public function __construct(
        string $type,
        ?string $url,
        ?int $age,
        ?string $userAgent,
        public readonly ?array $body,
    ) {
        parent::__construct($type, $url, $age, $userAgent);
    }

    public static function fromArray(array $data): static
    {
        return new static(
            type: $data['type'] ?? 'unknown',
            url: $data['url'] ?? null,
            age: $data['age'] ?? null,
            userAgent: $data['user_agent'] ?? null,
            body: $data['body'] ?? null,
        );
    }
}
