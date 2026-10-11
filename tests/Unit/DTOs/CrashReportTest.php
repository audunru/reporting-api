<?php

namespace audunru\ReportingApi\Tests\Unit\DTOs;

use audunru\ReportingApi\DTOs\CrashReport;
use PHPUnit\Framework\TestCase;

class CrashReportTest extends TestCase
{
    public function test_from_array_maps_all_properties(): void
    {
        $data = [
            'type' => 'crash',
            'url' => 'https://example.test/page',
            'age' => 0,
            'user_agent' => 'Mozilla/5.0',
            'body' => [
                'reason' => 'oom',
                'stack' => 'Error\n  at foo (app.js:1:1)',
                'is_top_level' => true,
                'visibility_state' => 'hidden',
            ],
        ];

        $report = CrashReport::fromArray($data);

        $this->assertSame('crash', $report->type);
        $this->assertSame('https://example.test/page', $report->url);
        $this->assertSame(0, $report->age);
        $this->assertSame('Mozilla/5.0', $report->userAgent);
        $this->assertSame('oom', $report->body->reason);
        $this->assertSame('Error\n  at foo (app.js:1:1)', $report->body->stack);
        $this->assertTrue($report->body->isTopLevel);
        $this->assertSame('hidden', $report->body->visibilityState);
    }

    public function test_from_array_defaults_missing_keys_to_null(): void
    {
        $report = CrashReport::fromArray([]);

        $this->assertNull($report->url);
        $this->assertNull($report->body->reason);
        $this->assertNull($report->body->stack);
        $this->assertNull($report->body->isTopLevel);
        $this->assertNull($report->body->visibilityState);
    }

    public function test_from_array_ignores_values_with_wrong_types(): void
    {
        $report = CrashReport::fromArray([
            'type' => 'crash',
            'url' => ['x'],
            'age' => 'abc',
            'user_agent' => 1,
            'body' => [
                'reason' => ['x'],
                'stack' => ['x'],
                'is_top_level' => ['x'],
                'visibility_state' => ['x'],
            ],
        ]);

        $this->assertNull($report->url);
        $this->assertNull($report->age);
        $this->assertNull($report->userAgent);
        foreach (get_object_vars($report->body) as $value) {
            $this->assertNull($value);
        }
    }

    public function test_from_array_ignores_body_with_wrong_type(): void
    {
        $report = CrashReport::fromArray(['type' => 'crash', 'body' => 'x']);

        foreach (get_object_vars($report->body) as $value) {
            $this->assertNull($value);
        }
    }
}
