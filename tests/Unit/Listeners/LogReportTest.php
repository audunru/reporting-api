<?php

namespace audunru\ReportingApi\Tests\Unit\Listeners;

use audunru\ReportingApi\DTOs\Report;
use audunru\ReportingApi\Events\CspViolationReceived;
use audunru\ReportingApi\Events\DeprecationReportReceived;
use audunru\ReportingApi\Events\GenericReportReceived;
use audunru\ReportingApi\Listeners\LogReport;
use audunru\ReportingApi\Tests\TestCase;
use Illuminate\Support\Facades\Log;

class LogReportTest extends TestCase
{
    public function test_logs_info_for_any_report(): void
    {
        $spy = Log::spy();
        $spy->shouldReceive('channel')->andReturnSelf();

        $event = new DeprecationReportReceived([
            'type' => 'deprecation',
            'url' => 'https://example.test/page',
            'body' => [
                'id' => 'some-feature',
                'message' => 'Feature is deprecated',
            ],
        ]);

        (new LogReport)->handle($event);

        $spy->shouldHaveReceived('warning')
            ->once()
            ->with('deprecation report received at https://example.test/page', \Mockery::type('array'));
    }

    public function test_replaces_line_breaks_in_message_values_with_spaces(): void
    {
        $spy = Log::spy();
        $spy->shouldReceive('channel')->andReturnSelf();

        (new LogReport)->handle(new GenericReportReceived([
            'type' => "x\nfake",
            'url' => "/a\n[2026-10-11 00:00:00] production.CRITICAL: Fake entry",
        ]));

        $spy->shouldHaveReceived('warning')
            ->once()
            ->with('x fake report received at /a [2026-10-11 00:00:00] production.CRITICAL: Fake entry', \Mockery::type('array'));
    }

    public function test_removes_query_strings_and_fragments_from_urls_in_the_message(): void
    {
        $spy = Log::spy();
        $spy->shouldReceive('channel')->andReturnSelf();

        (new LogReport)->handle(new DeprecationReportReceived([
            'type' => 'deprecation',
            'url' => 'https://example.test/page?token=SECRET#section',
            'body' => [
                'id' => 'some-feature',
                'message' => 'Feature is deprecated',
                'sourceFile' => 'https://example.test/app.js?v=1',
            ],
        ]));

        $spy->shouldHaveReceived('warning')
            ->once()
            ->with('deprecation report received at https://example.test/page', [
                'report' => [
                    'type' => 'deprecation',
                    'url' => 'https://example.test/page?token=SECRET#section',
                    'body' => [
                        'id' => 'some-feature',
                        'message' => 'Feature is deprecated',
                        'sourceFile' => 'https://example.test/app.js?v=1',
                    ],
                ],
            ]);
    }

    public function test_a_subclass_can_change_the_report_in_the_context(): void
    {
        $spy = Log::spy();
        $spy->shouldReceive('channel')->andReturnSelf();

        $listener = new class extends LogReport
        {
            protected function reportForLog(array $report): array
            {
                $report['url'] = $this->sanitizeUrlForLog($report['url']);

                return $report;
            }
        };

        $listener->handle(new DeprecationReportReceived([
            'type' => 'deprecation',
            'url' => 'https://example.test/reset?token=SECRET',
        ]));

        $spy->shouldHaveReceived('warning')
            ->once()
            ->with('deprecation report received at https://example.test/reset', [
                'report' => [
                    'type' => 'deprecation',
                    'url' => 'https://example.test/reset',
                ],
            ]);
    }

    public function test_skips_csp_violations_by_default(): void
    {
        $spy = Log::spy();

        (new LogReport)->handle(new CspViolationReceived([
            'type' => 'csp-violation',
            'url' => 'https://example.test/page',
            'body' => [],
        ]));

        $spy->shouldNotHaveReceived('channel');
    }

    public function test_skips_logging_when_excluded(): void
    {
        $spy = Log::spy();

        $listener = new class extends LogReport
        {
            protected function shouldExclude(Report $report): bool
            {
                return true;
            }
        };

        $listener->handle(new DeprecationReportReceived([
            'type' => 'deprecation',
            'body' => [],
        ]));

        $spy->shouldNotHaveReceived('channel');
    }
}
