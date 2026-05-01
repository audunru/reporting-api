<?php

namespace audunru\ReportingApi\Tests\Unit\DTOs;

use audunru\ReportingApi\DTOs\CspViolationReport;
use PHPUnit\Framework\TestCase;

class CspViolationReportTest extends TestCase
{
    public function test_from_array_maps_all_properties(): void
    {
        $data = [
            'type' => 'csp-violation',
            'url' => 'https://example.test/page',
            'age' => 10,
            'user_agent' => 'Mozilla/5.0',
            'body' => [
                'blockedURL' => 'https://evil.example/script.js',
                'columnNumber' => 5,
                'disposition' => 'enforce',
                'documentURL' => 'https://example.test/page',
                'effectiveDirective' => 'script-src-elem',
                'lineNumber' => 42,
                'originalPolicy' => "script-src 'self'",
                'referrer' => 'https://example.test/',
                'sample' => '',
                'sourceFile' => 'https://example.test/main.js',
                'statusCode' => 200,
            ],
        ];

        $report = CspViolationReport::fromArray($data);

        $this->assertSame('csp-violation', $report->type);
        $this->assertSame('https://example.test/page', $report->url);
        $this->assertSame(10, $report->age);
        $this->assertSame('Mozilla/5.0', $report->userAgent);
        $this->assertSame('https://evil.example/script.js', $report->body->blockedURL);
        $this->assertSame(5, $report->body->columnNumber);
        $this->assertSame('enforce', $report->body->disposition);
        $this->assertSame('https://example.test/page', $report->body->documentURL);
        $this->assertSame('script-src-elem', $report->body->effectiveDirective);
        $this->assertSame(42, $report->body->lineNumber);
        $this->assertSame("script-src 'self'", $report->body->originalPolicy);
        $this->assertSame('https://example.test/', $report->body->referrer);
        $this->assertSame('', $report->body->sample);
        $this->assertSame('https://example.test/main.js', $report->body->sourceFile);
        $this->assertSame(200, $report->body->statusCode);
    }

    public function test_from_array_defaults_missing_keys_to_null(): void
    {
        $report = CspViolationReport::fromArray(['type' => 'csp-violation']);

        $this->assertNull($report->url);
        $this->assertNull($report->age);
        $this->assertNull($report->userAgent);
        $this->assertNull($report->body->blockedURL);
        $this->assertNull($report->body->columnNumber);
        $this->assertNull($report->body->disposition);
        $this->assertNull($report->body->documentURL);
        $this->assertNull($report->body->effectiveDirective);
        $this->assertNull($report->body->lineNumber);
        $this->assertNull($report->body->originalPolicy);
        $this->assertNull($report->body->referrer);
        $this->assertNull($report->body->sample);
        $this->assertNull($report->body->sourceFile);
        $this->assertNull($report->body->statusCode);
    }
}
