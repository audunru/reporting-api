<?php

namespace audunru\ReportingApi\Tests\Feature;

use audunru\ReportingApi\Events\CspViolationReceived;
use audunru\ReportingApi\Events\GenericReportReceived;
use audunru\ReportingApi\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class RequestLimitTest extends TestCase
{
    public function test_default_max_body_size_is_sixty_four_kilobytes(): void
    {
        $this->assertSame(65536, config('reporting-api.max_body_size'));
    }

    public function test_reports_json_body_larger_than_limit_returns_payload_too_large(): void
    {
        Event::fake([GenericReportReceived::class]);

        $body = json_encode(array_fill(0, 10000, ['type' => 'x']));
        $this->assertGreaterThan(65536, strlen($body));

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], $body)
            ->assertStatus(413);

        Event::assertNotDispatched(GenericReportReceived::class);
    }

    public function test_csp_report_body_larger_than_limit_returns_payload_too_large(): void
    {
        Event::fake([CspViolationReceived::class]);

        $body = json_encode(['csp-report' => ['violated-directive' => str_repeat('a', 65536)]]);

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/csp-report'], $body)
            ->assertStatus(413);

        Event::assertNotDispatched(CspViolationReceived::class);
    }

    public function test_body_at_limit_is_accepted(): void
    {
        Event::fake([GenericReportReceived::class]);

        $body = json_encode([['type' => 'x']]);
        config()->set('reporting-api.max_body_size', strlen($body));

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], $body)
            ->assertNoContent();

        Event::assertDispatched(GenericReportReceived::class);
    }

    public function test_body_one_byte_over_configured_limit_returns_payload_too_large(): void
    {
        Event::fake([GenericReportReceived::class]);

        $body = json_encode([['type' => 'x']]);
        config()->set('reporting-api.max_body_size', strlen($body) - 1);

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], $body)
            ->assertStatus(413);

        Event::assertNotDispatched(GenericReportReceived::class);
    }

    public function test_default_max_reports_is_fifty(): void
    {
        $this->assertSame(50, config('reporting-api.max_reports'));
    }

    public function test_more_reports_than_default_limit_returns_payload_too_large(): void
    {
        Event::fake([GenericReportReceived::class]);

        $body = json_encode(array_fill(0, 51, ['type' => 'x']));

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], $body)
            ->assertStatus(413);

        Event::assertNotDispatched(GenericReportReceived::class);
    }

    public function test_reports_at_limit_are_accepted(): void
    {
        Event::fake([GenericReportReceived::class]);
        config()->set('reporting-api.max_reports', 2);

        $body = json_encode(array_fill(0, 2, ['type' => 'x']));

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], $body)
            ->assertNoContent();

        Event::assertDispatchedTimes(GenericReportReceived::class, 2);
    }

    public function test_one_report_over_configured_limit_returns_payload_too_large(): void
    {
        Event::fake([GenericReportReceived::class]);
        config()->set('reporting-api.max_reports', 2);

        $body = json_encode(array_fill(0, 3, ['type' => 'x']));

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/reports+json'], $body)
            ->assertStatus(413);

        Event::assertNotDispatched(GenericReportReceived::class);
    }
}
