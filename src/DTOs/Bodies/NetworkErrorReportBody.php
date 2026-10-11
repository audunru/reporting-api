<?php

namespace audunru\ReportingApi\DTOs\Bodies;

use audunru\ReportingApi\DTOs\Concerns\ReadsTypedValues;

/**
 * Body of a Network Error Logging report sent by the browser to a reporting endpoint.
 *
 * @see https://www.w3.org/TR/network-error-logging/
 */
class NetworkErrorReportBody
{
    use ReadsTypedValues;

    public function __construct(
        public readonly ?float $samplingFraction,
        public readonly ?int $elapsedTime,
        public readonly ?string $phase,
        public readonly ?string $type,
        public readonly ?string $serverIp,
        public readonly ?string $protocol,
        public readonly ?string $referrer,
        public readonly ?string $method,
        public readonly ?array $requestHeaders,
        public readonly ?array $responseHeaders,
        public readonly ?int $statusCode,
    ) {}

    public static function fromArray(array $data): static
    {
        // NEL spec uses snake_case keys for all body properties, unlike other report types
        return new static(
            samplingFraction: self::float($data, 'sampling_fraction'),
            elapsedTime: self::int($data, 'elapsed_time'),
            phase: self::string($data, 'phase'),
            type: self::string($data, 'type'),
            serverIp: self::string($data, 'server_ip'),
            protocol: self::string($data, 'protocol'),
            referrer: self::string($data, 'referrer'),
            method: self::string($data, 'method'),
            requestHeaders: self::array($data, 'request_headers'),
            responseHeaders: self::array($data, 'response_headers'),
            statusCode: self::int($data, 'status_code'),
        );
    }
}
