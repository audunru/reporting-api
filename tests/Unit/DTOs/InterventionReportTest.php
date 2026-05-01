<?php

namespace audunru\ReportingApi\Tests\Unit\DTOs;

use audunru\ReportingApi\DTOs\InterventionReport;
use PHPUnit\Framework\TestCase;

class InterventionReportTest extends TestCase
{
    public function test_from_array_maps_all_properties(): void
    {
        $data = [
            'type' => 'intervention',
            'url' => 'https://example.test/page',
            'age' => 0,
            'user_agent' => 'Mozilla/5.0',
            'body' => [
                'id' => 'HeavyAdIntervention',
                'message' => 'Ad was unloaded',
                'sourceFile' => 'https://example.test/ad.js',
                'lineNumber' => 12,
                'columnNumber' => 4,
            ],
        ];

        $report = InterventionReport::fromArray($data);

        $this->assertSame('intervention', $report->type);
        $this->assertSame('https://example.test/page', $report->url);
        $this->assertSame(0, $report->age);
        $this->assertSame('Mozilla/5.0', $report->userAgent);
        $this->assertSame('HeavyAdIntervention', $report->body->id);
        $this->assertSame('Ad was unloaded', $report->body->message);
        $this->assertSame('https://example.test/ad.js', $report->body->sourceFile);
        $this->assertSame(12, $report->body->lineNumber);
        $this->assertSame(4, $report->body->columnNumber);
    }

    public function test_from_array_defaults_missing_keys_to_null(): void
    {
        $report = InterventionReport::fromArray([]);

        $this->assertNull($report->url);
        $this->assertNull($report->body->id);
        $this->assertNull($report->body->message);
        $this->assertNull($report->body->sourceFile);
        $this->assertNull($report->body->lineNumber);
        $this->assertNull($report->body->columnNumber);
    }
}
