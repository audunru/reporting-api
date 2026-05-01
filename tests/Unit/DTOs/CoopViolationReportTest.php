<?php

namespace audunru\ReportingApi\Tests\Unit\DTOs;

use audunru\ReportingApi\DTOs\CoopViolationReport;
use PHPUnit\Framework\TestCase;

class CoopViolationReportTest extends TestCase
{
    public function test_from_array_maps_navigation_violation(): void
    {
        $data = [
            'type' => 'coop',
            'url' => 'https://example.test/page',
            'age' => 0,
            'user_agent' => 'Mozilla/5.0',
            'body' => [
                'type' => 'navigate-to-document',
                'disposition' => 'enforce',
                'effectivePolicy' => 'same-origin',
                'previousResponseURL' => 'https://example.test/prev',
                'nextResponseURL' => null,
                'referrer' => 'https://example.test/',
            ],
        ];

        $report = CoopViolationReport::fromArray($data);

        $this->assertSame('coop', $report->type);
        $this->assertSame('navigate-to-document', $report->body->type);
        $this->assertSame('enforce', $report->body->disposition);
        $this->assertSame('same-origin', $report->body->effectivePolicy);
        $this->assertSame('https://example.test/prev', $report->body->previousResponseURL);
        $this->assertNull($report->body->nextResponseURL);
        $this->assertSame('https://example.test/', $report->body->referrer);
    }

    public function test_from_array_maps_access_violation(): void
    {
        $data = [
            'type' => 'coop',
            'url' => 'https://example.test/page',
            'age' => 0,
            'user_agent' => 'Mozilla/5.0',
            'body' => [
                'type' => 'access-to-opener',
                'disposition' => 'reporting',
                'effectivePolicy' => 'same-origin',
                'property' => 'postMessage',
                'sourceFile' => 'https://example.test/app.js',
                'lineNumber' => 7,
                'columnNumber' => 2,
            ],
        ];

        $report = CoopViolationReport::fromArray($data);

        $this->assertSame('access-to-opener', $report->body->type);
        $this->assertSame('postMessage', $report->body->property);
        $this->assertSame('https://example.test/app.js', $report->body->sourceFile);
        $this->assertSame(7, $report->body->lineNumber);
        $this->assertSame(2, $report->body->columnNumber);
    }

    public function test_from_array_defaults_missing_keys_to_null(): void
    {
        $report = CoopViolationReport::fromArray([]);

        $this->assertNull($report->url);
        $this->assertNull($report->body->type);
        $this->assertNull($report->body->disposition);
        $this->assertNull($report->body->effectivePolicy);
        $this->assertNull($report->body->previousResponseURL);
        $this->assertNull($report->body->nextResponseURL);
        $this->assertNull($report->body->referrer);
        $this->assertNull($report->body->property);
        $this->assertNull($report->body->sourceFile);
        $this->assertNull($report->body->lineNumber);
        $this->assertNull($report->body->columnNumber);
    }
}
