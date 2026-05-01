<?php

namespace audunru\ReportingApi\Tests\Unit\DTOs;

use audunru\ReportingApi\DTOs\GenericReport;
use PHPUnit\Framework\TestCase;

class GenericReportTest extends TestCase
{
    public function test_from_array_maps_all_properties(): void
    {
        $data = [
            'type' => 'custom-type',
            'url' => 'https://example.test/page',
            'age' => 0,
            'user_agent' => 'Mozilla/5.0',
            'body' => ['detail' => 'some data'],
        ];

        $report = GenericReport::fromArray($data);

        $this->assertSame('custom-type', $report->type);
        $this->assertSame('https://example.test/page', $report->url);
        $this->assertSame(0, $report->age);
        $this->assertSame('Mozilla/5.0', $report->userAgent);
        $this->assertSame(['detail' => 'some data'], $report->body);
    }

    public function test_from_array_defaults_type_to_unknown(): void
    {
        $report = GenericReport::fromArray([]);

        $this->assertSame('unknown', $report->type);
        $this->assertNull($report->url);
        $this->assertNull($report->age);
        $this->assertNull($report->userAgent);
        $this->assertNull($report->body);
    }
}
