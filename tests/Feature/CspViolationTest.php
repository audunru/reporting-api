<?php

namespace audunru\ReportingApi\Tests\Feature;

use audunru\ReportingApi\Events\CspViolationReceived;
use audunru\ReportingApi\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class CspViolationTest extends TestCase
{
    public function test_legacy_format_dispatches_event(): void
    {
        Event::fake();

        $body = [
            'violated-directive' => 'script-src',
            'document-uri' => 'https://example.test/page',
            'blocked-uri' => 'inline',
            'status-code' => 200,
        ];

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/csp-report'], json_encode(['csp-report' => $body]))
            ->assertNoContent();

        Event::assertDispatched(CspViolationReceived::class, function (CspViolationReceived $event) use ($body) {
            return $event->getRawReport() === ['type' => 'csp-violation', 'body' => $body];
        });
    }

    public function test_modern_format_dispatches_event(): void
    {
        Event::fake();

        $report = [
            'type' => 'csp-violation',
            'age' => 10,
            'url' => 'https://example.test/page',
            'user_agent' => 'Mozilla/5.0',
            'body' => [
                'violated-directive' => 'script-src',
                'blocked-uri' => 'inline',
            ],
        ];

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], json_encode([$report]))
            ->assertNoContent();

        Event::assertDispatched(CspViolationReceived::class, function (CspViolationReceived $event) use ($report) {
            return $event->getRawReport() === $report;
        });
    }

    public function test_legacy_format_silently_ignores_missing_csp_report_key(): void
    {
        Event::fake([CspViolationReceived::class]);

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/csp-report'], json_encode(['invalid' => []]))
            ->assertNoContent();

        Event::assertNotDispatched(CspViolationReceived::class);
    }
}
