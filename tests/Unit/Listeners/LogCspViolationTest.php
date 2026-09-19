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

        $report = [
            'type' => 'csp-violation',
            'url' => 'https://example.test/page',
            'body' => [
                'effectiveDirective' => 'script-src',
                'blockedURL' => 'https://evil.example/script.js',
            ],
        ];

        (new LogCspViolation)->handle(new CspViolationReceived($report));

        $spy->shouldHaveReceived('warning')
            ->once()
            ->with('CSP violation: script-src blocked https://evil.example/script.js on https://example.test/page', [
                'page' => 'https://example.test/page',
                'report' => $report,
            ]);
    }

    public function test_logs_the_full_report_in_the_context(): void
    {
        $spy = Log::spy();
        $spy->shouldReceive('channel')->andReturnSelf();

        $report = [
            'type' => 'csp-violation',
            'age' => 10,
            'url' => 'https://example.test/page',
            'user_agent' => 'Mozilla/5.0',
            'body' => [
                'effectiveDirective' => 'script-src',
                'blockedURL' => 'https://evil.example/script.js',
                'sourceFile' => 'https://example.test/app.js',
                'lineNumber' => 42,
                'columnNumber' => 7,
                'sample' => 'eval("...")',
                'documentURL' => 'https://example.test/page',
                'disposition' => 'enforce',
            ],
        ];

        (new LogCspViolation)->handle(new CspViolationReceived($report));

        $spy->shouldHaveReceived('warning')
            ->once()
            ->with(\Mockery::type('string'), [
                'page' => 'https://example.test/page',
                'report' => $report,
            ]);
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
            'url' => 'https://example.test/page',
            'body' => [
                'effectiveDirective' => 'script-src',
                'blockedURL' => 'https://evil.example/script.js',
            ],
        ]));

        $spy->shouldNotHaveReceived('channel');
    }

    public function test_logs_a_report_a_subclass_does_not_exclude(): void
    {
        $spy = Log::spy();
        $spy->shouldReceive('channel')->andReturnSelf();

        $listener = new class extends LogCspViolation
        {
            protected function shouldExclude(CspViolationReport $report): bool
            {
                return false;
            }
        };

        $listener->handle(new CspViolationReceived([
            'type' => 'csp-violation',
            'url' => 'https://example.test/page',
            'body' => [
                'effectiveDirective' => 'script-src',
                'blockedURL' => 'https://evil.example/script.js',
            ],
        ]));

        $spy->shouldHaveReceived('warning')->once();
    }

    public function test_skips_reports_with_an_empty_body(): void
    {
        $spy = Log::spy();

        (new LogCspViolation)->handle(new CspViolationReceived([
            'type' => 'csp-violation',
            'url' => 'https://example.test/page',
            'body' => [],
        ]));

        $spy->shouldNotHaveReceived('channel');
    }

    public function test_skips_reports_without_an_effective_directive(): void
    {
        $spy = Log::spy();

        (new LogCspViolation)->handle(new CspViolationReceived([
            'type' => 'csp-violation',
            'url' => 'https://example.test/page',
            'body' => [
                'blockedURL' => 'https://evil.example/script.js',
            ],
        ]));

        $spy->shouldNotHaveReceived('channel');
    }

    public function test_skips_reports_without_a_blocked_url(): void
    {
        $spy = Log::spy();

        (new LogCspViolation)->handle(new CspViolationReceived([
            'type' => 'csp-violation',
            'url' => 'https://example.test/page',
            'body' => [
                'effectiveDirective' => 'script-src',
            ],
        ]));

        $spy->shouldNotHaveReceived('channel');
    }
}
