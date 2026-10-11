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
            type: self::string($data, 'type') ?? 'unknown',
            url: self::string($data, 'url'),
            age: self::int($data, 'age'),
            userAgent: self::string($data, 'user_agent'),
            body: self::array($data, 'body'),
        );
    }
}
