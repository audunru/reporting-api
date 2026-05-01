<?php

namespace audunru\ReportingApi\DTOs\Bodies;

/**
 * Body of a Network Error Logging report sent by the browser to a reporting endpoint.
 *
 * @see https://www.w3.org/TR/network-error-logging/
 */
class NetworkErrorReportBody
{
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
            samplingFraction: $data['sampling_fraction'] ?? null,
            elapsedTime: $data['elapsed_time'] ?? null,
            phase: $data['phase'] ?? null,
            type: $data['type'] ?? null,
            serverIp: $data['server_ip'] ?? null,
            protocol: $data['protocol'] ?? null,
            referrer: $data['referrer'] ?? null,
            method: $data['method'] ?? null,
            requestHeaders: $data['request_headers'] ?? null,
            responseHeaders: $data['response_headers'] ?? null,
            statusCode: $data['status_code'] ?? null,
        );
    }
}
