<?php

namespace audunru\ReportingApi\Tests\Unit\Listeners;

use audunru\ReportingApi\DTOs\Report;
use audunru\ReportingApi\Events\CspViolationReceived;
use audunru\ReportingApi\Events\DeprecationReportReceived;
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
