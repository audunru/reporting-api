<?php

namespace audunru\ReportingApi\Tests\Feature;

use audunru\ReportingApi\Events\CspViolationReceived;
use audunru\ReportingApi\Listeners\LogCspViolation;
use audunru\ReportingApi\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class CspViolationTest extends TestCase
{
    private const LEGACY_BODY = [
        'document-uri' => 'https://app.test/page',
        'referrer' => 'https://app.test/',
        'blocked-uri' => 'https://evil.example/x.js',
        'effective-directive' => 'script-src-elem',
        'violated-directive' => 'script-src-elem',
        'original-policy' => "script-src 'self'",
        'disposition' => 'enforce',
        'status-code' => 200,
        'source-file' => 'https://app.test/app.js',
        'line-number' => 12,
        'column-number' => 3,
        'script-sample' => 'alert(1)',
    ];

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

        Event::assertDispatched(CspViolationReceived::class, function (CspViolationReceived $event) {
            return $event->getRawReport() === [
                'type' => 'csp-violation',
                'url' => 'https://example.test/page',
                'body' => [
                    'blockedURL' => 'inline',
                    'documentURL' => 'https://example.test/page',
                    'statusCode' => 200,
                    'effectiveDirective' => 'script-src',
                ],
            ];
        });
    }

    public function test_legacy_format_maps_keys_to_reporting_api_names(): void
    {
        Event::fake();

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/csp-report'], json_encode(['csp-report' => self::LEGACY_BODY]))
            ->assertNoContent();

        $report = Event::dispatched(CspViolationReceived::class)[0][0]->getReport();

        $this->assertSame('https://app.test/page', $report->url);
        $this->assertSame('https://evil.example/x.js', $report->body->blockedURL);
        $this->assertSame('https://app.test/page', $report->body->documentURL);
        $this->assertSame('script-src-elem', $report->body->effectiveDirective);
        $this->assertSame("script-src 'self'", $report->body->originalPolicy);
        $this->assertSame('enforce', $report->body->disposition);
        $this->assertSame(200, $report->body->statusCode);
        $this->assertSame('https://app.test/app.js', $report->body->sourceFile);
        $this->assertSame(12, $report->body->lineNumber);
        $this->assertSame(3, $report->body->columnNumber);
        $this->assertSame('alert(1)', $report->body->sample);
        $this->assertSame('https://app.test/', $report->body->referrer);
    }

    public function test_legacy_format_is_logged(): void
    {
        $spy = Log::spy();
        $spy->shouldReceive('channel')->andReturnSelf();
        Event::listen(CspViolationReceived::class, LogCspViolation::class);

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/csp-report'], json_encode(['csp-report' => self::LEGACY_BODY]))
            ->assertNoContent();

        $spy->shouldHaveReceived('warning')
            ->once()
            ->withArgs(fn (string $message) => $message === 'CSP violation: script-src-elem blocked https://evil.example/x.js on https://app.test/page');
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

    public function test_legacy_format_with_wrong_body_type_does_not_fail(): void
    {
        $this->withoutExceptionHandling();
        Event::listen(CspViolationReceived::class, LogCspViolation::class);

        $this->call('POST', '/reports', [], [], [], ['CONTENT_TYPE' => 'application/csp-report'], json_encode(['csp-report' => 'x']))
            ->assertNoContent();
    }
}
