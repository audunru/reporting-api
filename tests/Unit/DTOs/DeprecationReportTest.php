<?php

namespace audunru\ReportingApi\Tests\Unit\DTOs;

use audunru\ReportingApi\DTOs\DeprecationReport;
use PHPUnit\Framework\TestCase;

class DeprecationReportTest extends TestCase
{
    public function test_from_array_maps_all_properties(): void
    {
        $data = [
            'type' => 'deprecation',
            'url' => 'https://example.test/page',
            'age' => 5,
            'user_agent' => 'Mozilla/5.0',
            'body' => [
                'id' => 'NavigatorGetUserMedia',
                'message' => 'getUserMedia() is deprecated',
                'sourceFile' => 'https://example.test/app.js',
                'lineNumber' => 10,
                'columnNumber' => 3,
                'anticipatedRemoval' => '2025-01-01',
            ],
        ];

        $report = DeprecationReport::fromArray($data);

        $this->assertSame('deprecation', $report->type);
        $this->assertSame('https://example.test/page', $report->url);
        $this->assertSame(5, $report->age);
        $this->assertSame('Mozilla/5.0', $report->userAgent);
        $this->assertSame('NavigatorGetUserMedia', $report->body->id);
        $this->assertSame('getUserMedia() is deprecated', $report->body->message);
        $this->assertSame('https://example.test/app.js', $report->body->sourceFile);
        $this->assertSame(10, $report->body->lineNumber);
        $this->assertSame(3, $report->body->columnNumber);
        $this->assertSame('2025-01-01', $report->body->anticipatedRemoval);
    }

    public function test_from_array_defaults_missing_keys_to_null(): void
    {
        $report = DeprecationReport::fromArray([]);

        $this->assertNull($report->url);
        $this->assertNull($report->age);
        $this->assertNull($report->userAgent);
        $this->assertNull($report->body->id);
        $this->assertNull($report->body->message);
        $this->assertNull($report->body->sourceFile);
        $this->assertNull($report->body->lineNumber);
        $this->assertNull($report->body->columnNumber);
        $this->assertNull($report->body->anticipatedRemoval);
    }
}
