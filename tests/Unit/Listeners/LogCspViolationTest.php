<?php

namespace audunru\ReportingApi\Tests\Unit\Listeners;

use audunru\ReportingApi\DTOs\CspViolationReport;
use audunru\ReportingApi\Events\CspViolationReceived;
use audunru\ReportingApi\Listeners\LogCspViolation;
use audunru\ReportingApi\Tests\TestCase;
use Illuminate\Support\Facades\Log;

class LogCspViolationTest extends TestCase
{
    public function test_logs_warning_for_csp_violation(): void
    {
        $spy = Log::spy();
        $spy->shouldReceive('channel')->andReturnSelf();

        $event = new CspViolationReceived([
            'type' => 'csp-violation',
            'url' => 'https://example.test/page',
            'body' => [
                'effectiveDirective' => 'script-src',
                'blockedURL' => 'https://evil.example/script.js',
            ],
        ]);

        (new LogCspViolation)->handle($event);

        $spy->shouldHaveReceived('warning')->once();
    }

    public function test_skips_logging_when_excluded(): void
    {
        $spy = Log::spy();

        $listener = new class extends LogCspViolation
        {
            protected function shouldExclude(CspViolationReport $report): bool
            {
                return true;
            }
        };

        $listener->handle(new CspViolationReceived([
            'type' => 'csp-violation',
            'body' => [],
        ]));

        $spy->shouldNotHaveReceived('channel');
    }
}
