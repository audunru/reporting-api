<?php

namespace audunru\ReportingApi\Tests\Unit\DTOs;

use audunru\ReportingApi\DTOs\NetworkErrorReport;
use PHPUnit\Framework\TestCase;

class NetworkErrorReportTest extends TestCase
{
    public function test_from_array_maps_all_properties(): void
    {
        $data = [
            'type' => 'network-error',
            'url' => 'https://example.test/page',
            'age' => 0,
            'user_agent' => 'Mozilla/5.0',
            'body' => [
                'sampling_fraction' => 0.01,
                'elapsed_time' => 42,
                'phase' => 'dns',
                'type' => 'dns.name_not_resolved',
                'server_ip' => '1.2.3.4',
                'protocol' => 'h2',
                'referrer' => 'https://example.test',
                'method' => 'GET',
                'request_headers' => ['Accept' => ['text/html']],
                'response_headers' => [],
                'status_code' => 0,
            ],
        ];

        $report = NetworkErrorReport::fromArray($data);

        $this->assertSame('network-error', $report->type);
        $this->assertSame('https://example.test/page', $report->url);
        $this->assertSame(0, $report->age);
        $this->assertSame('Mozilla/5.0', $report->userAgent);
        $this->assertSame(0.01, $report->body->samplingFraction);
        $this->assertSame(42, $report->body->elapsedTime);
        $this->assertSame('dns', $report->body->phase);
        $this->assertSame('dns.name_not_resolved', $report->body->type);
        $this->assertSame('1.2.3.4', $report->body->serverIp);
        $this->assertSame('h2', $report->body->protocol);
        $this->assertSame('https://example.test', $report->body->referrer);
        $this->assertSame('GET', $report->body->method);
        $this->assertSame(['Accept' => ['text/html']], $report->body->requestHeaders);
        $this->assertSame([], $report->body->responseHeaders);
        $this->assertSame(0, $report->body->statusCode);
    }

    public function test_from_array_defaults_missing_keys_to_null(): void
    {
        $report = NetworkErrorReport::fromArray([]);

        $this->assertNull($report->url);
        $this->assertNull($report->body->samplingFraction);
        $this->assertNull($report->body->elapsedTime);
        $this->assertNull($report->body->phase);
        $this->assertNull($report->body->type);
        $this->assertNull($report->body->serverIp);
        $this->assertNull($report->body->protocol);
        $this->assertNull($report->body->referrer);
        $this->assertNull($report->body->method);
        $this->assertNull($report->body->requestHeaders);
        $this->assertNull($report->body->responseHeaders);
        $this->assertNull($report->body->statusCode);
    }
}
