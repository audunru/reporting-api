<?php

namespace audunru\ReportingApi\Tests\Feature;

use audunru\ReportingApi\Events\CspViolationReceived;
use audunru\ReportingApi\Events\GenericReportReceived;
use audunru\ReportingApi\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class ContentTypeRoutingTest extends TestCase
{
    public function test_unsupported_content_type_returns_bad_request(): void
    {
        Event::fake([CspViolationReceived::class, GenericReportReceived::class]);

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'text/plain'], '{}')
            ->assertStatus(400);

        Event::assertNotDispatched(CspViolationReceived::class);
        Event::assertNotDispatched(GenericReportReceived::class);
    }

    public function test_missing_content_type_returns_bad_request(): void
    {
        Event::fake([CspViolationReceived::class, GenericReportReceived::class]);

        $this->call('POST', '/reports', [], [], [], [], '{}')
            ->assertStatus(400);

        Event::assertNotDispatched(CspViolationReceived::class);
        Event::assertNotDispatched(GenericReportReceived::class);
    }

    public function test_application_json_content_type_returns_bad_request(): void
    {
        Event::fake([CspViolationReceived::class, GenericReportReceived::class]);

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/json'], '{}')
            ->assertStatus(400);

        Event::assertNotDispatched(CspViolationReceived::class);
        Event::assertNotDispatched(GenericReportReceived::class);
    }

    public function test_csp_report_content_type_returns_no_content(): void
    {
        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/csp-report'], json_encode(['csp-report' => ['violated-directive' => 'script-src']]))
            ->assertNoContent();
    }

    public function test_reports_json_content_type_returns_no_content(): void
    {
        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], json_encode([]))
            ->assertNoContent();
    }

    public function test_reports_json_content_type_with_charset_param_returns_no_content(): void
    {
        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json; charset=utf-8'], json_encode([]))
            ->assertNoContent();
    }
}
