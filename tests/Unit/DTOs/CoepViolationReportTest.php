<?php

namespace audunru\ReportingApi\Tests\Unit\DTOs;

use audunru\ReportingApi\DTOs\CoepViolationReport;
use PHPUnit\Framework\TestCase;

class CoepViolationReportTest extends TestCase
{
    public function test_from_array_maps_all_properties(): void
    {
        $data = [
            'type' => 'coep',
            'url' => 'https://example.test/page',
            'age' => 0,
            'user_agent' => 'Mozilla/5.0',
            'body' => [
                'type' => 'corp',
                'blockedURL' => 'https://cdn.example.com/script.js',
                'destination' => 'script',
                'disposition' => 'enforce',
            ],
        ];

        $report = CoepViolationReport::fromArray($data);

        $this->assertSame('coep', $report->type);
        $this->assertSame('https://example.test/page', $report->url);
        $this->assertSame(0, $report->age);
        $this->assertSame('Mozilla/5.0', $report->userAgent);
        $this->assertSame('corp', $report->body->type);
        $this->assertSame('https://cdn.example.com/script.js', $report->body->blockedURL);
        $this->assertSame('script', $report->body->destination);
        $this->assertSame('enforce', $report->body->disposition);
    }

    public function test_from_array_defaults_missing_keys_to_null(): void
    {
        $report = CoepViolationReport::fromArray([]);

        $this->assertNull($report->url);
        $this->assertNull($report->body->type);
        $this->assertNull($report->body->blockedURL);
        $this->assertNull($report->body->destination);
        $this->assertNull($report->body->disposition);
    }
}
